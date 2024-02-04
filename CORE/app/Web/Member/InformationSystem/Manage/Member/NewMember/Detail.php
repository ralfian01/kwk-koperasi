<?php

namespace App\Web\Member\InformationSystem\Manage\Member\NewMember;

use App\Web\Member\BaseMember;
use App\REST\V1\Manage\Member\Register\Get as GetNewMemberList;

class Detail extends BaseMember
{
    /* Edit this line to set privilege rules */
    protected $privilegeRules = [
        'MEMBER_MANAGE_VIEW'
    ];

    /* Edit this line to set page header */
    protected $pageHead = [
        'title' => 'Konfirmasi calon anggota baru',
        'go_back' => true,
    ];

    /**
     * Main activity
     * @return void
     */
    protected function mainActivity($id = null)
    {
        $payload['id'] = base64_encode($id);

        // Try to get member data
        $newMemberData = (new GetNewMemberList(...[
            'payload' => $payload,
            'auth' => $this->auth
        ]))->get();

        if (isset($newMemberData['code']) && $newMemberData['code'] == 404) {
            return $this->error(404);
        }

        return $this->view("information_system/manage/member/new/detail/index", [
            'newMemberData' => $newMemberData,
        ]);
    }
}
