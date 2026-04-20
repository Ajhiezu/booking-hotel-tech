<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewOwnerRegisteredNotification extends Notification
{
    use Queueable;

    public function __construct(protected User $owner)
    {
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'type'    => 'new_owner',
            'title'   => 'New Hotel Owner Registered',
            'message' => "{$this->owner->name} has registered as a hotel owner and is awaiting verification.",
            'action'  => route('admin.users.show', $this->owner->id),
            'icon'    => 'user-plus',
        ];
    }
}
