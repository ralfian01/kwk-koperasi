<?php

namespace App\Models\FinanceReport;

use MVCME\Models\DynModel;

class FinanceReport extends DynModel
{
    /*
     * ---------------------------------------------
     * SETUP DATABASE TABLE
     * ---------------------------------------------
     */

    protected $table = 'pts_finance_report';
    protected $primaryKey = 'pfr_id';
    protected $returnType = 'array';
    protected $allowedFields = [
        "pfr_reportType", "pfr_description", "pfr_amount", "pfr_createdBy", "pfr_metaDeletable"
    ];
    protected $useAutoIncrement = true;
    protected $skipValidation = true;
    protected $useTimestamps = false;


    /* NEW WRITING STANDARD */

    /*
     * ---------------------------------------------
     * SETUP SQL STANDARD
     * ---------------------------------------------
     */

    protected $columnAliases = [
        ["pfr_id", "finance_report_id"],
        ["pfr_reportType", "finance_report_type"],
        ["pfr_description", "finance_report_description"],
        ["pfr_amount", "finance_report_amount"],
        ["pfr_createdAt", "finance_report_date"],
        ["pfr_metaDeletable", "deletable"],
        ["pfr_createdBy", "reporter_id"],
        // 
        [[
            "member_id" => "pfr_createdBy",
            "member_fullname" => "pts_member__identity.pmi_fullname",
            "member_register_number" => "pts_member.pm_registerNumber"
        ], "reporter"]
    ];

    protected $filterData = [
        "finance_report_id" => ["pfr_id", "where"],
        "finance_report_type" => ["pfr_reportType", "whereIn"],
        "finance_report_description" => ["pfr_description", "like"],
        "finance_report_amount" => ["pfr_amount", "like"],
        "finance_report_date" => ["pfr_createdAt", "where"],
        "deletable" => ["pfr_metaDeletable", "where"],
    ];

    protected $tableRelations = [
        ["pts_account", "pts_account.pa_id = pts_finance_report.pfr_createdBy", "LEFT"],
        ["pts_member", "pts_member.pa_id = pts_account.pa_id", "LEFT"],
        ["pts_member__identity", "pts_member__identity.pm_id = pts_member.pm_id", "LEFT"],
    ];


    /*
     * ---------------------------------------------
     * RETURN DATA 
     * ---------------------------------------------
     */

    /**
     * Filters:
     * - finance_report_id
     * - finance_report_type
     * - finance_report_description
     * - finance_report_amount
     * - finance_report_date
     * - deletable
     * 
     * Method to get all found data in array
     * @param null|int|array $limit [offset, limit]
     * @return array|null|object
     */
    public function all(array $filter = [], $limit = null)
    {
        // Apply standard SQL builder
        $this
            ->setStdColumnAlias()
            ->setStdTableRelation()
            ->setStdFilter($filter)
            ->setLimiter($limit);

        // Collect data
        $result = $this->find();

        if ($result == null) return null;

        // Parse the column that has JSON value
        foreach ($result as $key => $value) {
            if (isset($value['reporter'])) $value['reporter'] = json_decode($value['reporter'], true);

            $result[$key] = $value;
        }
        return $result;
    }

    /**
     * Filters:
     * - report_id
     * 
     * Method to get 1 row specific data with more detailed data
     * @return array|null|object
     */
    public function data(?array $identifier = [])
    {
        // Override filter alias
        $filter = [
            "finance_report_id" => ["pfr_id", "where"],
        ];

        $this
            ->setStdColumnAlias()
            ->setFilter($filter, $identifier)
            ->setStdTableRelation();

        $result = $this->get()->getResultArray();

        if ($result != null) {

            $result = $result[0];

            // Parse the column that has JSON value
            if (isset($result['reporter'])) $result['reporter'] = json_decode($result['reporter'], true);
        }
        return $result;
    }
}
