<?php

namespace App\Policies;

use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class VideoPolicy
{
    use HandlesAuthorization;

    public function rate(User $user)
    {
        return $user->hasPermission('videos.rate');
    }

    public function index(User $user)
    {
        return $user->hasPermission('videos.view');
    }

    public function show(User $user)
    {
        return $user->hasPermission('videos.view');
    }

    public function store(User $user)
    {
        return $user->hasPermission('videos.create');
    }

    public function update(User $user)
    {
        return $user->hasPermission('videos.update');
    }

    public function destroy(User $user)
    {
        return $user->hasPermission('videos.delete');
    }
}
