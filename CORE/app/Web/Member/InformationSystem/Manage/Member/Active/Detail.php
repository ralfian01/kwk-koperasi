<?php

namespace App\Web\Member\InformationSystem\Manage\Member\Active;

use App\Web\Member\BaseMember;
use App\REST\V1\Manage\Member\Get as GetMemberList;

class Detail extends BaseMember
{
    /* Edit this line to set privilege rules */
    protected $privilegeRules = [
        'MEMBER_MANAGE_VIEW'
    ];

    /* Edit this line to set page header */
    protected $pageHead = [
        'title' => 'Data anggota',
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
        $memberData = (new GetMemberList(...[
            'payload' => $payload,
            'auth' => $this->auth
        ]))->get();

        if (isset($memberData['code']) && $memberData['code'] == 404) {
            return $this->error(404);
        }

        return $this->view("information_system/manage/member/active/detail/index", [
            'memberData' => $memberData,
        ]);
    }
}
