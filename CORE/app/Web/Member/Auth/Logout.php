<?php

namespace App\Web\Member\Auth;

use App\Web\Member\BaseMember;

/**
 * Logout
 */
class Logout extends BaseMember
{
    /* Edit this line to set privilege rules */
    protected $privilegeRules = [];

    /**
     * Main activity
     * @return void
     */
    protected function mainActivity()
    {
        setcookie('_PTS-Auth:Token', '', time() - 3600);

        return $this->response->redirect(member_url(''));
    }
}
