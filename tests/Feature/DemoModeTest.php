<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class DemoModeTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['demo.enabled' => true, 'app.key' => 'base64:'.base64_encode(random_bytes(32))]);
        $this->withoutVite();
        DB::connection()->beforeExecuting(function () {
            throw new \RuntimeException('Demo mode attempted to access the database.');
        });
    }

    public function test_all_demo_pages_render_without_database_access(): void
    {
        foreach (Route::getRoutes() as $route) {
            if (! in_array('GET', $route->methods()) || ! in_array('web', $route->middleware())) {
                continue;
            }
            if (in_array($route->getName(), ['demo.role', 'dashboard', 'notifications.read', 'verification.verify'])) {
                continue;
            }
            $parameters = [];
            foreach ($route->parameterNames() as $parameter) {
                $parameters[$parameter] = match ($parameter) {
                    'hotel' => 'bali-ocean-retreat', 'token' => 'demo-token', default => 1,
                };
            }
            $url = route($route->getName(), $parameters);
            $response = $this->get($url);
            $this->assertSame(200, $response->status(), 'Demo page failed: '.$url.' '.substr($response->getContent(), 0, 300));
            $response->assertSee('Demo Mode');
        }
    }

    public function test_role_switching_and_forms_only_simulate_actions(): void
    {
        $this->get('/demo/super_admin')->assertRedirect('/admin/dashboard');
        $this->get('/dashboard')->assertRedirect('/admin/dashboard');
        $this->post('/customer/book/1', ['guest_name' => 'Demo Guest'])
            ->assertRedirect('/customer/bookings/1')->assertSessionHas('success');
        $this->delete('/admin/users/1')->assertRedirect()->assertSessionHas('success');
        $this->get('/demo/unknown')->assertNotFound();
        $this->get('/hotels/not-a-demo-hotel')->assertNotFound();
    }

    public function test_hotel_search_filters_sample_data(): void
    {
        $this->get('/hotels/search?location=Bali')->assertOk()
            ->assertSee('Bali Ocean Retreat')->assertDontSee('Jakarta Grand Hotel');
        $this->get('/hotels/search?max_price=400000')->assertOk()
            ->assertSee('Yogyakarta Heritage')->assertDontSee('Bali Ocean Retreat');
        $this->get('/hotels/search?facilities[]=1')->assertOk()->assertSee('Bali Ocean Retreat');
        $this->get('/hotels/search?facilities[]=999')->assertOk()->assertDontSee('Bali Ocean Retreat');
    }

    public function test_disabling_demo_mode_restores_authentication(): void
    {
        config(['demo.enabled' => false]);
        $this->get('/admin/dashboard')->assertRedirect('/login');
        $this->get('/demo/super_admin')->assertNotFound();
    }
}
