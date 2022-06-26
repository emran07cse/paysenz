<?php

namespace App\Policies;

use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PaymentOptionPolicy
{
    use HandlesAuthorization;

    public function before(User $user, $paymentOption)
    {
        if($user->isAdmin()) return true;
        else return null;
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
