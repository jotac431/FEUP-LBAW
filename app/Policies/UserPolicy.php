<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Auth;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can update the account.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\User  $model
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, User $model)
    {
        return $user->id === $model->id;
    }

    /**
     * Determine whether the user can delete the account.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\User  $model
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, User $model)
    {
        return $user->id === $model->id;
    }

    /**
     * Determine whether the user can report another user.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\User  $model
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function report(User $user, User $model)
    {
        return Auth::check();
    }

    /**
     * Determine whether the user can see the suspension info of another user.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\User  $model
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function suspension(User $user, User $model)
    {
        return ($user->is_suspended && $user->id === $model->id)
            || $user->is_admin;
    }

    /**
     * Determine whether the user can see another user's followed users.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\User  $model
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function followed(User $user, User $model)
    {
        return $user->id === $model->id;
    }

    /**
     * Determine whether the user can follow another user.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\User  $model
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function follow(User $user, User $model)
    {
        return Auth::check();
    }

    /**
     * Determine whether the user can unfollow another user.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\User  $model
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function unfollow(User $user, User $model)
    {
        return Auth::check();
    }

    /**
     * Determine whether the user can suspend other users.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function suspendUser(User $user, User $userToSuspend)
    {
        return $user->is_admin && !$userToSuspend->is_admin;
    }

    /**
     * Determine whether the user can unsuspend other users.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function unsuspendUser(User $user, User $userToUnsuspend)
    {
        return $user->is_admin && !$userToUnsuspend->is_admin;
    }

    /**
     * Determine whether the user can see and manage notifications.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\User  $model
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function notifications(User $user)
    {
        return Auth::check();
    }
}
