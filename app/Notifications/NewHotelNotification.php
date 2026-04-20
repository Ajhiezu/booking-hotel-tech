<?php

namespace App\Notifications;

use App\Models\Hotel;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewHotelNotification extends Notification
{
    use Queueable;

    public function __construct(protected Hotel $hotel)
    {
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'type'    => 'new_hotel',
            'title'   => 'New Hotel Submitted',
            'message' => "{$this->hotel->owner->name} has submitted a new hotel '{$this->hotel->name}' for approval.",
            'action'  => route('admin.hotels.show', $this->hotel->id),
            'icon'    => 'office-building',
        ];
    }
}
