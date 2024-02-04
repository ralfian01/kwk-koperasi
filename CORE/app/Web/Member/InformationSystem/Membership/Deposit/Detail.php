<?php

namespace App\Web\Member\InformationSystem\Membership\Deposit;

use App\Web\Member\BaseMember;
use App\REST\V1\Member\Deposit\Get as GetMemberDeposit;

class Detail extends BaseMember
{
    /* Edit this line to set privilege rules */
    protected $privilegeRules = [
        'MEMBER_DEPOSIT_VIEW'
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
        $payload['id'] = $id;

        // Try to get member data
        $memberDepositData = (new GetMemberDeposit(...[
            'payload' => $payload,
            'auth' => $this->auth
        ]))->get();

        if (isset($memberDepositData['code']) && $memberDepositData['code'] == 404) {
            return $this->error(404);
        }

        return $this->view("information_system/membership/deposit/detail/index", [
            'memberDepositData' => $memberDepositData
        ]);
    }
}
