<?php

namespace App\Web\Member\InformationSystem\MyAccount;

use App\Web\Member\BaseMember;
// use App\REST\V1\Member\Get as GetMember;
// use App\Models\Deposit;

class Setting extends BaseMember
{
    /* Edit this line to set privilege rules */
    protected $privilegeRules = [];

    /* Edit this line to set page header */
    protected $pageHead = [
        'title' => 'Akun saya',
        // 'go_back' => true,
    ];

    /**
     * Main activity
     * @return void
     */
    protected function mainActivity($id = null)
    {
        return $this->view("information_system/my_account/setting");
    }
}
