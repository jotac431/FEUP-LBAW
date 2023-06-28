<?php

namespace App\Policies;

use App\Models\Tag;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Auth;

class TagPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can accept a tag.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function accept(User $user)
    {
        return $user->is_admin;
    }

    /**
     * Determine whether the user can reject a tag.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function reject(User $user)
    {
        return $user->is_admin;
    }

    /**
     * Determine whether the user can add a tag to its favorites
     */
    public function addFavorite(User $user)
    {
        return Auth::check();
    }

    /**
     * Determine whether the user can add a tag to its favorites
     */
    public function removeFavorite(User $user)
    {
        return Auth::check();
    }

    public function showFilteredTags(User $user)
    {
        return $user->is_admin;
    }

    public function destroy(User $user)
    {
        return $user->is_admin;
    }

    public function propose(User $user)
    {
        return Auth::check();
    }
}
