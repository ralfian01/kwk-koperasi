<?php

namespace App\Web\Member\InformationSystem\Manage\Member\NewMember;

use App\Web\Member\BaseMember;
use App\REST\V1\Manage\Member\Register\Get as GetNewMemberList;

class Lists extends BaseMember
{
    /* Edit this line to set privilege rules */
    protected $privilegeRules = [
        'MEMBER_MANAGE_VIEW'
    ];

    /* Edit this line to set page header */
    protected $pageHead = [
        'title' => 'Kelola anggota baru',
        'description' => 'Daftar anggota baru koperasi'
    ];

    /**
     * Main activity
     * @return void
     */
    protected function mainActivity($id = null)
    {
        $filter = $this->getSearchFilter();

        // Try to get member data
        $newMemberList = (new GetNewMemberList(...[
            'payload' => $filter,
            'auth' => $this->auth
        ]))->get();

        if (isset($newMemberList['code']) && $newMemberList['code'] == 404) {
            $newMemberList = null;
        }

        if (!isset($filter['status']) || !isset($filter['pagination'])) {
            return $this->fillInSearchFilter();
        }

        return $this->view("information_system/manage/member/new/list", [
            'newMemberList' => $newMemberList,
            'filter' => $filter
        ]);
    }


    protected function fillInSearchFilter()
    {
        $filter = $this->getSearchFilter();

        if (!isset($filter['status'])) {
            $filter['status'] = 'WT_VALIDATION,REGISTER_REJECT,WT_PAYMENT,WT_PAYMENT_VALIDATION';
        } else {
            $filter['status'] = implode(',', $filter['status']);
        }

        if (!isset($filter['pagination'])) {
            $filter['pagination'] = '20,0';
        }

        return $this->response->redirect(member_url("manage/member/new?status={$filter['status']}&pagination={$filter['pagination']}"));
    }

    protected function getSearchFilter()
    {
        $filter = $this->request->getGetPost();
        $filter = array_merge(
            $filter,
            removeNullValues([
                'state_code' => isset($filter['status']) ? explode(',', $filter['status']) : null,
                // 'state_code_in' => isset($filter['status']) ? explode(',', $filter['status']) : null,
                'status' => isset($filter['status']) ? explode(',', $filter['status']) : null,
                'limit' => $filter['pagination']
            ])
        );

        return $filter;
    }
}
