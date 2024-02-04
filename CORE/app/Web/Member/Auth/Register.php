<?php

namespace App\Web\Member\Auth;

use App\Web\Member\BaseMember;

/**
 * Login.
 * The class works the other way around where when the user
 * has not authorized it, it displays the page.
 * Conversely, if the user is authorized, then redirect
 * the user to the dashboard page
 */
class Register extends BaseMember
{
    /* Edit this line to set privilege rules */
    protected $privilegeRules = [];

    protected function __unauthorizedScheme()
    {
        return $this->view('auth/register/index');
    }

    /**
     * Main activity
     * @return void
     */
    protected function mainActivity()
    {
        return $this->response->redirect(member_url());
    }
}
