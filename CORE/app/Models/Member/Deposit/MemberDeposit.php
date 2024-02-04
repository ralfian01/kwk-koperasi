<?php

namespace App\Models\Member\Deposit;

use MVCME\Models\DynModel;

class MemberDeposit extends DynModel
{
    /*
     * ---------------------------------------------
     * SETUP DATABASE TABLE
     * ---------------------------------------------
     */

    protected $table = 'pts_member__deposit';
    protected $primaryKey = 'pmd_id';
    protected $returnType = 'array';
    protected $allowedFields = [
        "pm_id", "pd_id", "pmd_amount", "pmd_updatedAt", "pmd_metaStatusActive"
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
        ["pm_id", "member_id"],
        ["pmd_id", "deposit_id"],
        ["pmd_amount", "deposit_amount"],
        ["pmd_createdAt", "created_at"],
        ["pmd_updatedAt", "updated_at"],
        // pd
        ["pts_deposit.pd_code", "deposit_type"],
        // pmdp
        [[
            "status" => "pts_member__deposit_payment.pmdp_paymentStatus",
            "method" => "pts_member__deposit_payment.pmdp_paymentMethod"
        ], "payment"],
        // pa
        ["pts_account.pa_id", "account_id"],
        ["pts_account.pa_username", "username"],
    ];

    protected $filterData = [
        "member_id" => ["pm_id", "where"],
        "deposit_id" => ["pmd_id", "where"],
        "created_at" => ["pmd_createdAt", "where"],
        "actived" => ["pmd_metaStatusActive", "whereIn"],
        // pmdp
        "payment_status" => ["pts_member__deposit_payment.pmdp_paymentStatus", "whereIn"],
        // pd
        "deposit_type" => ["pts_deposit.pd_code", "whereIn"],
        // pa
        "account_id" => ["pts_account.pa_id", "where"],
        "uuid" => ["pts_account.pa_uuid", "where"],
    ];

    protected $tableRelations = [
        ["pts_member", "pts_member.pm_id = pts_member__deposit.pm_id", "LEFT"],
        ["pts_deposit", "pts_deposit.pd_id = pts_member__deposit.pd_id", "LEFT"],
        ["pts_member__deposit_payment", "pts_member__deposit_payment.pmd_id = pts_member__deposit.pmd_id", "LEFT"],
        ["pts_account", "pts_account.pa_id = pts_member.pa_id", "LEFT"],
    ];


    /*
     * ---------------------------------------------
     * RETURN DATA 
     * ---------------------------------------------
     */

    /**
     * Filters:
     * - uuid
     * - member_id
     * - deposit_type
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
            if (isset($value['payment'])) $value['payment'] = json_decode($value['payment'], true);

            $result[$key] = $value;
        }
        return $result;
    }

    /**
     * Filters:
     * - id
     * - uuid
     * 
     * Method to get 1 row specific data with more detailed data
     * @return array|null|object
     */
    public function data(?array $identifier = [])
    {
        // Override filter alias
        $filter = [
            "id" => ["pa_id", "where"],
            "uuid" => ["pts_account.pa_uuid", "where"]
        ];

        $this
            ->setStdColumnAlias()
            ->setFilter($filter, $identifier)
            ->setStdTableRelation();

        $result = $this->get()->getResultArray();

        if ($result != null) {

            $result = $result[0];

            // Parse the column that has JSON value
            if (isset($result['payment'])) $result['payment'] = json_decode($result['payment'], true);
        }
        return $result;
    }
}
