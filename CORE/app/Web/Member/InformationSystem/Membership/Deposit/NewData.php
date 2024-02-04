<?php

namespace App\Web\Member\InformationSystem\Membership\Deposit;

use App\Web\Member\BaseMember;
use App\REST\V1\Member\Get as GetMember;
use App\Models\Deposit;

class NewData extends BaseMember
{
    /* Edit this line to set privilege rules */
    protected $privilegeRules = [
        'MEMBER_DEPOSIT_VIEW',
        'MEMBER_DEPOSIT_ADD',
    ];

    /* Edit this line to set page header */
    protected $pageHead = [
        'title' => 'Bayar simpanan keanggotaan',
        'go_back' => true,
    ];

    /**
     * Main activity
     * @return void
     */
    protected function mainActivity($id = null)
    {
        // Try to get deposit info
        $deposit = (new Deposit())->all();

        // Try to get member data
        $member = (new GetMember(...['auth' => $this->auth]))->get();

        if (isset($member['code']) && $member['code'] == 404) {
            $member = null;
        }

        return $this->view("information_system/membership/deposit/new", [
            'member' => $member,
            'deposit' => $deposit
        ]);
    }
}
