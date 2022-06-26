<?php

namespace App\Policies;

use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Auth;

class UserPolicy
{
    use HandlesAuthorization;

    public function before(User $user, $userData)
    {
        if($user->isAdmin()) return true;
        return null;
    }

    /**
     * Determine whether the user can view all users.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function index(User $user)
    {

    }

    /**
     * Determine whether the user can view the user.
     *
     * @param  \App\User  $user
     * @param  \App\User  $user
     * @return mixed
     */
    public function view(User $user, User $userData)
    {
        return $user->id == $userData->id;
    }

    /**
     * Determine whether the user can create users.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {

    }

    /**
     * Determine whether the user can update the user.
     * A user can only update another  if he is an Admin
     * A manager and he has greater access than the user to be update
     * Admin can update everybody
     * Manager can update trainer and student
     *
     * @param  \App\User  $user
     * @param  \App\User  $user
     * @return mixed
     */
    public function update(User $user, User $userData)
    {
        if($user->id == $userData->id) return true;
    }

    /**
     * Determine whether the user can delete the user.
     *
     * @param  \App\User  $user
     * @param  \App\User  $user
     * @return mixed
     */
    public function delete(User $user, User $userData)
    {

    }
}