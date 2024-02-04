<?php

namespace App\Web\Member\InformationSystem\Manage\FinanceReport\Income;

use App\Web\Member\BaseMember;
use App\REST\V1\Manage\FinanceReport\Income\Get as GetFinanceReportIncome;

class Lists extends BaseMember
{
    /* Edit this line to set privilege rules */
    protected $privilegeRules = [
        'FINANCE_REPORT_MANAGE_VIEW'
    ];

    /* Edit this line to set page header */
    protected $pageHead = [
        'title' => 'Laporan pemasukan',
    ];

    /**
     * Main activity
     * @return void
     */
    protected function mainActivity($id = null)
    {
        $filter = $this->getSearchFilter();

        // Try to get member data
        $financeReportIncomeList = (new GetFinanceReportIncome(...[
            'payload' => $filter,
            'auth' => $this->auth
        ]))->get();

        if (isset($financeReportIncomeList['code']) && $financeReportIncomeList['code'] == 404) {
            $financeReportIncomeList = null;
        }

        if (!isset($filter['pagination'])) {
            return $this->fillInSearchFilter();
        }

        return $this->view("information_system/manage/finance_report/income/list", [
            'financeReportIncomeList' => $financeReportIncomeList,
            'filter' => $filter
        ]);
    }


    protected function fillInSearchFilter()
    {
        $filter = $this->getSearchFilter();

        if (!isset($filter['pagination'])) {
            $filter['pagination'] = '20,0';
        }

        return $this->response
            ->redirect(
                member_url(
                    "manage/finance_report/income?pagination={$filter['pagination']}"
                )
            );
    }

    protected function getSearchFilter()
    {
        $filter = $this->request->getGetPost();
        $filter = array_merge(
            $filter,
            removeNullValues([
                'date_range' => isset($filter['date_range']) ? explode(',', $filter['date_range']) : null,
                'limit' => $filter['pagination']
            ])
        );

        return $filter;
    }
}
