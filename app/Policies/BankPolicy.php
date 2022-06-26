<?php

namespace App\Policies;

use App\User;
use App\Bank;
use Illuminate\Auth\Access\HandlesAuthorization;

class BankPolicy
{
    use HandlesAuthorization;

    public function before(User $user, $bank)
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can view all banks.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function index(User $user)
    {

    }

    /**
     * Determine whether the user can view the bank.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function view(User $user)
    {

    }

    /**
     * Determine whether the user can create banks.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {

    }

    /**
     * Determine whether the user can update the bank.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function update(User $user)
    {

    }

    /**
     * Determine whether the user can delete the bank.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function delete(User $user)
    {

    }
}