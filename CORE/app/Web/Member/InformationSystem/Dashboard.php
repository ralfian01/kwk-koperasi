<?php

namespace App\Web\Member\InformationSystem;

use App\Web\Member\BaseMember;

class Dashboard extends BaseMember
{
    /* Edit this line to set privilege rules */
    protected $privilegeRules = [];

    /* Edit this line to set page header */
    protected $pageHead = [
        'title' => 'Dashboard',
        'description' => 'Selamat datang di sistem informasi anggota koperasi'
    ];

    /**
     * Main activity
     * @return void
     */
    protected function mainActivity()
    {
        return $this->view("information_system/dashboard/index");
    }
}
