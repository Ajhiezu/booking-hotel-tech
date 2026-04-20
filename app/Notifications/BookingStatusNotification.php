<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class BookingStatusNotification extends Notification
{
    use Queueable;

    public function __construct(protected Booking $booking, protected string $causerRole)
    {
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        $status = ucfirst($this->booking->status);
        $roleLabel = $this->causerRole === 'customer' ? 'Customer' : 'Hotel Owner';
        
        // Determine the action URL based on recipient's role
        $routeName = $notifiable->isHotelOwner() ? 'owner.bookings.show' : 'customer.bookings.show';

        return [
            'type'    => 'booking_cancelled',
            'title'   => "Booking {$status}",
            'message' => "Booking #{$this->booking->booking_code} has been {$this->booking->status} by the {$roleLabel}.",
            'action'  => route($routeName, $this->booking->id),
            'icon'    => 'calendar-x',
        ];
    }
}
