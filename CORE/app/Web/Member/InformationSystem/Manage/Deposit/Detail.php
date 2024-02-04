<?php

namespace App\Web\Member\InformationSystem\Manage\Deposit;

use App\Web\Member\BaseMember;
use App\REST\V1\Manage\Member\DepositPayment\Get as GetMemberDepositPayment;

class Detail extends BaseMember
{
    /* Edit this line to set privilege rules */
    protected $privilegeRules = [
        'DEPOSIT_MANAGE_VIEW',
        'DEPOSIT_MANAGE_APPROVE'
    ];

    /* Edit this line to set page header */
    protected $pageHead = [
        'title' => 'Konfirmasi pembayaran simpanan anggota',
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
        $memberDepositPaymentData = (new GetMemberDepositPayment(...[
            'payload' => $payload,
            'auth' => $this->auth
        ]))->get();

        if (isset($memberDepositPaymentData['code']) && $memberDepositPaymentData['code'] == 404) {
            return $this->error(404);
        }

        return $this->view("information_system/manage/deposit/detail/index", [
            'memberDepositPaymentData' => $memberDepositPaymentData
        ]);
    }
}
