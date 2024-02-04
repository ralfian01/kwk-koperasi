<?php

namespace App\Web\Member\InformationSystem\Membership;

use App\Web\Member\BaseMember;
use App\REST\V1\Member\Get as GetMember;

class Registration extends BaseMember
{
    /* Edit this line to set privilege rules */
    protected $privilegeRules = [];

    /* Edit this line to set page header */
    protected $pageHead = [
        'title' => 'Daftar anggota baru',
        'go_back' => true
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
            return $this->view("information_system/membership/register");
        }

        return $this->error(403);
    }
}
