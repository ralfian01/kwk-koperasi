<?php

namespace App\Web\Member\InformationSystem\Manage\Member\Active;

use App\Web\Member\BaseMember;
use App\REST\V1\Manage\Member\Get as GetMemberList;

class Lists extends BaseMember
{
    /* Edit this line to set privilege rules */
    protected $privilegeRules = [
        'MEMBER_MANAGE_VIEW'
    ];

    /* Edit this line to set page header */
    protected $pageHead = [
        'title' => 'Kelola anggota',
        'description' => 'Daftar anggota koperasi'
    ];

    /**
     * Main activity
     * @return void
     */
    protected function mainActivity()
    {
        $filter = $this->getSearchFilter();

        // Try to get member data
        $memberList = (new GetMemberList(...[
            'payload' => $filter,
            'auth' => $this->auth
        ]))->get();

        if (isset($memberList['code']) && $memberList['code'] == 404) {
            $memberList = null;
        }

        if (!isset($filter['actived']) || !isset($filter['pagination'])) {
            return $this->fillInSearchFilter();
        }

        return $this->view("information_system/manage/member/active/list", [
            'memberList' => $memberList,
            'filter' => $filter
        ]);
    }

    protected function fillInSearchFilter()
    {
        $filter = $this->getSearchFilter();

        if (!isset($filter['actived'])) {
            $filter['actived'] = '1,0';
        } else {
            $filter['actived'] = implode(',', $filter['actived']);
        }

        if (!isset($filter['pagination'])) {
            $filter['pagination'] = '20,0';
        }

        return $this->response
            ->redirect(
                member_url("manage/member?actived={$filter['actived']}&pagination={$filter['pagination']}")
            );
    }

    protected function getSearchFilter()
    {
        $filter = $this->request->getGetPost();
        $filter = array_merge(
            $filter,
            removeNullValues([
                'actived' => isset($filter['actived']) ? explode(',', $filter['actived']) : null,
                'limit' => $filter['pagination']
            ])
        );

        return $filter;
    }
}
