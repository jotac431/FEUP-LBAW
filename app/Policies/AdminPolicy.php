<?php

namespace App\Policies;

use App\Models\Admin;
use App\Models\Report;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AdminPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the admin panel.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function show(User $user)
    {
        return $user->is_admin;
    }

    /**
     * Determine whether the user can view the list of suspended users.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function suspensions(User $user)
    {
        return $user->is_admin;
    }

    public function suspendUser(User $user) {
        return Auth::user()->is_admin && !$user->is_admin;
    }

    /**
     * Determine whether the user can view the list of reports.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function reports(User $user)
    {
        return $user->is_admin;
    }

    /**
     * Determine whether the user can manage tags.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function tags(User $user)
    {
        return $user->is_admin;
    }

    /**
     * Determine whether the user can close reports.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function closeReport(User $user, Report $report)
    {
        return $user->is_admin && $report->reported_id !== $user->id;
    }
}
