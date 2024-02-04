<?php

namespace App\Web\Member\Recovery;

use App\Web\Member\BaseMember;

class ResetPassword extends BaseMember
{
    public function index()
    {
        return $this->view("welcome_message");
    }
}
