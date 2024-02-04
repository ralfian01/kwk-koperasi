<?php

namespace App\Web\Member\InformationSystem\Membership;

use App\Web\Member\BaseMember;
use App\REST\V1\Member\Get as GetMember;

class Setting extends BaseMember
{
    /* Edit this line to set privilege rules */
    protected $privilegeRules = [];

    /* Edit this line to set page header */
    protected $pageHead = [
        'title' => 'Atur data anggota',
        'description' => 'Atur data umum, identitas, dan usaha'
    ];

    /**
     * Main activity
     * @return void
     */
    protected function mainActivity()
    {
        // Try to get member data
        $member = (new GetMember(...['auth' => $this->auth]))->get();

        if (isset($member['code']) && $member['code'] == 404) {
            $member = null;
        }

        return $this->view("information_system/membership/setting/home", [
            'member' => $member
        ]);
    }
}
