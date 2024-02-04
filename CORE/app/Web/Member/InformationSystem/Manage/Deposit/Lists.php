<?php

namespace App\Web\Member\InformationSystem\Manage\Deposit;

use App\Web\Member\BaseMember;
use App\REST\V1\Manage\Member\DepositPayment\Get as GetMemberDepositPaymentList;

class Lists extends BaseMember
{
    /* Edit this line to set privilege rules */
    protected $privilegeRules = [
        'DEPOSIT_MANAGE_VIEW'
    ];

    /* Edit this line to set page header */
    protected $pageHead = [
        'title' => 'Kelola simpanan',
        'description' => 'Daftar simpanan anggota'
    ];

    /**
     * Main activity
     * @return void
     */
    protected function mainActivity($id = null)
    {
        $filter = $this->getSearchFilter();

        // Try to get member data
        $memberDepositList = (new GetMemberDepositPaymentList(...[
            'payload' => $filter,
            'auth' => $this->auth
        ]))->get();

        if (isset($memberDepositList['code']) && $memberDepositList['code'] == 404) {
            $memberDepositList = null;
        }

        if (
            !isset($filter['payment_status_in'])
            || !isset($filter['deposit_type_in'])
            || !isset($filter['payment_method_in'])
            || !isset($filter['pagination'])
        ) {
            return $this->fillInSearchFilter();
        }

        return $this->view("information_system/manage/deposit/list", [
            'memberDepositList' => $memberDepositList,
            'filter' => $filter
        ]);
    }


    protected function fillInSearchFilter()
    {
        $filter = $this->getSearchFilter();

        if (!isset($filter['payment_status_in'])) {
            $filter['payment_status_in'] = 'PENDING,VALID,INVALID';
        } else {
            $filter['payment_status_in'] = implode(',', $filter['payment_status_in']);
        }

        if (!isset($filter['deposit_type_in'])) {
            $filter['deposit_type_in'] = 'BASE,MANDATORY,VOLUNTARY';
        } else {
            $filter['deposit_type_in'] = implode(',', $filter['deposit_type_in']);
        }

        if (!isset($filter['payment_method_in'])) {
            $filter['payment_method_in'] = 'CASH,TRANSFER';
        } else {
            $filter['payment_method_in'] = implode(',', $filter['payment_method_in']);
        }

        if (!isset($filter['pagination'])) {
            $filter['pagination'] = '20,0';
        }

        return $this->response
            ->redirect(
                member_url(
                    "manage/deposit?payment_status_in={$filter['payment_status_in']}&payment_method_in={$filter['payment_method_in']}&deposit_type_in={$filter['deposit_type_in']}&pagination={$filter['pagination']}"
                )
            );
    }

    protected function getSearchFilter()
    {
        $filter = $this->request->getGetPost();
        $filter = array_merge(
            $filter,
            removeNullValues([
                'payment_status_in' => isset($filter['payment_status_in']) ? explode(',', $filter['payment_status_in']) : null,
                'payment_method_in' => isset($filter['payment_method_in']) ? explode(',', $filter['payment_method_in']) : null,
                'deposit_type_in' => isset($filter['deposit_type_in']) ? explode(',', $filter['deposit_type_in']) : null,
                'limit' => $filter['pagination']
            ])
        );

        return $filter;
    }
}
