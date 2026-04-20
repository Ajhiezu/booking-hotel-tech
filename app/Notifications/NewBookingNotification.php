<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewBookingNotification extends Notification
{
    use Queueable;

    public function __construct(protected Booking $booking)
    {
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'type'    => 'new_booking',
            'title'   => 'New Booking Received',
            'message' => "Guest '{$this->booking->guest_name}' has booked a room at '{$this->booking->hotel->name}'.",
            'action'  => route('owner.bookings.show', $this->booking->id),
            'icon'    => 'calendar-check',
        ];
    }
}
