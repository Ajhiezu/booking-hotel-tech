<?php

namespace App\Policies;

use App\Models\Hotel;
use App\Models\User;

class HotelPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Hotel $hotel): bool
    {
        return $user->isSuperAdmin() || $hotel->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->isHotelOwner() && $user->is_active;
    }

    public function update(User $user, Hotel $hotel): bool
    {
        return $user->isSuperAdmin() || $hotel->user_id === $user->id;
    }

    public function delete(User $user, Hotel $hotel): bool
    {
        return $user->isSuperAdmin() || $hotel->user_id === $user->id;
    }
}
