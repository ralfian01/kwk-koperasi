<?php

namespace App\Models\Member\Deposit;

use MVCME\Models\DynModel;

class MemberDepositPayment extends DynModel
{
    /*
     * ---------------------------------------------
     * SETUP DATABASE TABLE
     * ---------------------------------------------
     */

    protected $table = 'pts_member__deposit_payment';
    protected $primaryKey = 'pmdp_id';
    protected $returnType = 'array';
    protected $allowedFields = [
        "pmd_id", "pmdp_paymentMethod", "pmdp_paymentProof", "pmdp_paymentStatus", "pmdp_verifiedBy"
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
        ["pmdp_id", "payment_id"],
        ["pmdp_paymentMethod", "payment_method"],
        ["pmdp_paymentStatus", "payment_status"],
        // pd
        ["pmd_id", "deposit_id"],
        ["pts_deposit.pd_code", "deposit_type"],
        // pmd
        ["pts_member__deposit.pmd_amount", "deposit_amount"],
        ["pts_member__deposit.pmd_createdAt", "created_at"],
        ["pts_member__deposit.pmd_updatedAt", "updated_at"],
        // pm
        ["pts_member.pm_id", "member_id"],
        ["pts_member__identity.pmi_fullname", "member_fullname"],
        ["pts_member.pm_registerNumber", "member_register_number"],
        // pa
        ["pts_account.pa_uuid", "uuid"],
        ["pts_account.pa_username", "username"],
        // pav
        ["pts_account_verifier.pa_username", "verified_by"],
        // 
        [[
            "member_id" => "pts_member_verifier.pm_id",
            "register_number" => "pts_member_verifier.pm_registerNumber",
            "fullname" => "pts_member__identity_verifier.pmi_fullname",
        ], "verifier"],
    ];

    protected $filterData = [
        "deposit_id" => ["pmd_id", "where"],
        "payment_id" => ["pmdp_id", "where"],
        "payment_status" => ["pmdp_paymentStatus", "whereIn"],
        "payment_method" => ["pmdp_paymentMethod", "whereIn"],
        "verified_by" => ["pmdp_verifiedBy", "where"],
        // pmd
        "created_at" => ["pts_member__deposit.pmd_createdAt >=", "where"],
        "actived" => ["pts_member__deposit.pmd_metaStatusActive", "where"],
        // pd
        "deposit_type" => ["pts_deposit.pd_code", "whereIn"],
        // pm
        "member_id" => ["pts_member.pm_id", "where"],
        // pa
        "uuid" => ["pts_account.pa_uuid", "where"],
    ];

    protected $tableRelations = [
        ["pts_member__deposit", "pts_member__deposit.pmd_id = pts_member__deposit_payment.pmd_id", "LEFT"],
        ["pts_deposit", "pts_deposit.pd_id = pts_member__deposit.pd_id", "LEFT"],
        ["pts_member", "pts_member.pm_id = pts_member__deposit.pm_id", "LEFT"],
        ["pts_member__identity", "pts_member__identity.pm_id = pts_member__deposit.pm_id", "LEFT"],
        ["pts_account", "pts_account.pa_id = pts_member.pa_id", "LEFT"],
        // Verifier
        ["pts_account pts_account_verifier", "pts_account_verifier.pa_id = pts_member__deposit_payment.pmdp_verifiedBy", "LEFT"],
        ["pts_member pts_member_verifier", "pts_member_verifier.pa_id = pts_account_verifier.pa_id", "LEFT"],
        ["pts_member__identity pts_member__identity_verifier", "pts_member__identity_verifier.pm_id = pts_member_verifier.pm_id", "LEFT"],
    ];


    /*
     * ---------------------------------------------
     * RETURN DATA 
     * ---------------------------------------------
     */

    /**
     * Filters:
     * - member_id
     * - registration_number
     * - registration_type
     * - business_name
     * - business_npwp
     * - business_phone_number
     * - business_email
     * - registration_date
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
            if (isset($value['pm_metaState'])) $value['pm_metaState'] = json_decode($value['pm_metaState'], true);
            if (isset($value['verifier'])) $value['verifier'] = json_decode($value['verifier'], true);

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
            "uuid" => ["pa_uuid", "where"]
        ];

        $this
            ->setStdColumnAlias()
            ->setFilter($filter, $identifier)
            ->setStdTableRelation();

        $result = $this->get()->getResultArray();

        if ($result != null) {

            $result = $result[0];

            // Parse the column that has JSON value
            if (isset($result['pm_metaState'])) $result['pm_metaState'] = json_decode($result['pm_metaState'], true);
            if (isset($result['verifier'])) $result['verifier'] = json_decode($result['verifier'], true);
        }
        return $result;
    }
}
