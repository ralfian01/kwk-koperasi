<?php

namespace App\Web\Member\InformationSystem\Membership\Deposit;

use App\Web\Member\BaseMember;
use App\REST\V1\Member\Deposit\Get as GetMemberDeposit;

class Lists extends BaseMember
{
    /* Edit this line to set privilege rules */
    protected $privilegeRules = [
        'MEMBER_DEPOSIT_VIEW'
    ];

    /* Edit this line to set page header */
    protected $pageHead = [
        'title' => 'Simpanan keanggotaan',
        'description' => 'Daftar simpanan aktif',
    ];

    /**
     * Main activity
     * @return void
     */
    protected function mainActivity($id = null)
    {
        $filter = $this->getSearchFilter();

        // Try to get member data
        $memberDeposit = (new GetMemberDeposit(...[
            'payload' => $filter,
            'auth' => $this->auth
        ]))->get();

        if (isset($memberDeposit['code']) && $memberDeposit['code'] == 404) {
            $memberDeposit = null;
        }

        if (
            !isset($filter['payment_status_in'])
            || !isset($filter['deposit_type'])
            || !isset($filter['pagination'])
        ) {
            return $this->fillInSearchFilter();
        }

        return $this->view("information_system/membership/deposit/list", [
            'memberDeposit' => $memberDeposit,
            'filter' => $filter
        ]);
    }


    protected function fillInSearchFilter()
    {
        $filter = $this->getSearchFilter();

        if (!isset($filter['payment_status_in'])) {
            $filter['payment_status_in'] = 'NOT_PAID,PENDING,VALID,INVALID';
        } else {
            $filter['payment_status_in'] = implode(',', $filter['payment_status_in']);
        }

        if (!isset($filter['deposit_type'])) {
            $filter['deposit_type'] = 'BASE,MANDATORY,VOLUNTARY';
        } else {
            $filter['deposit_type'] = implode(',', $filter['deposit_type']);
        }

        if (!isset($filter['pagination'])) {
            $filter['pagination'] = '20,0';
        }

        return $this->response
            ->redirect(
                member_url(
                    "membership/deposit?payment_status_in={$filter['payment_status_in']}&deposit_type={$filter['deposit_type']}&pagination={$filter['pagination']}"
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
                'deposit_type' => isset($filter['deposit_type']) ? explode(',', $filter['deposit_type']) : null,
                'limit' => $filter['pagination']
            ])
        );

        return $filter;
    }
}
