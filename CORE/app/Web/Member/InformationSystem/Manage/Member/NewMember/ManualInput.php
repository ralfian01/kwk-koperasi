<?php

namespace App\Web\Member\InformationSystem\Manage\Member\NewMember;

use App\Web\Member\BaseMember;

class ManualInput extends BaseMember
{
    /* Edit this line to set privilege rules */
    protected $privilegeRules = [
        'MEMBER_MANAGE_VIEW',
        'MEMBER_MANAGE_INSERT',
    ];

    /* Edit this line to set page header */
    protected $pageHead = [
        'title' => 'Input data anggota manual',
    ];

    /**
     * Main activity
     * @return void
     */
    protected function mainActivity($id = null)
    {

        return $this->view("information_system/manage/member/new/manual_input");
    }
}
