<?php

namespace App\Web\Member\InformationSystem\Manage\FinanceReport\Income;

use App\Web\Member\BaseMember;

class NewData extends BaseMember
{
    /* Edit this line to set privilege rules */
    protected $privilegeRules = [
        'FINANCE_REPORT_MANAGE_VIEW',
        'FINANCE_REPORT_MANAGE_ADD'
    ];

    /* Edit this line to set page header */
    protected $pageHead = [
        'title' => 'Tambah laporan pemasukan',
        'go_back' => true,
    ];

    /**
     * Main activity
     * @return void
     */
    protected function mainActivity($id = null)
    {
        return $this->view("information_system/manage/finance_report/income/new");
    }
}
