<?php

namespace App\Policies;

use App\Models\Content;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Access\HandlesAuthorization;

class ContentPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can create models.
     * 
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        return Auth::check();
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Article  $article
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Content $content)
    {
        return $user->id === $content->author()->first()->id;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Content  $Content
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Content $content)
    {
        return $user->id === $content->author()->first()->id || $user->is_admin;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function updateFeedback(User $user, Content $content)
    {
        if (!Auth::check()) return false;
        if (!isset($content->author)) return true;

        return $user->id !== $content->author->id;
    }

}
