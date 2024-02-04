<?php

namespace App\Models\Member\Deposit;

use MVCME\Models\DynModel;

class DepositView extends DynModel
{
    /*
     * ---------------------------------------------
     * SETUP DATABASE TABLE
     * ---------------------------------------------
     */

    protected $table = 'pts__member_deposit';
    protected $primaryKey = 'pmd_id';
    protected $returnType = 'array';


    /* NEW WRITING STANDARD */

    /*
     * ---------------------------------------------
     * SETUP SQL STANDARD
     * ---------------------------------------------
     */

    protected $columnAliases = [
        ["pm_id", "member_id"],
        ["pmd_id", "payment_id"],
        ["pmd_depositType", "deposit_type"],
        ["pmd_amount", "deposit_amount"],
        // 
        ["pts__member_deposit_payment.pmdp_paymentMethod", "payment_method"],
        ["pts__member_deposit_payment.pmdp_amount", "amount"],
        ["pts__member_deposit_payment.pmdp_paymentDate", "payment_date"],
        ["pts__member_deposit_payment.pmdp_paymentStatus", "payment_status"],
        // 
        ["pts_account.pa_id", "account_id"],
        ["pts_account.pa_username", "username"],
    ];

    protected $filterData = [
        "uuid" => ["pts_account.pa_uuid", "where"],
        "member_id" => ["pm_id", "where"],
        "deposit_type" => ["pmd_depositType", "whereIn"],
        "payment_method" => ["pts__member_deposit_payment.pmdp_paymentMethod", "whereIn"],
        "payment_date" => ["pts__member_deposit_payment.pmdp_paymentDate", "whereIn"],
    ];

    protected $tableRelations = [
        ["pts_member", "pts_member.pm_id = pts__member_deposit.pm_id", "LEFT"],
        ["pts__member_deposit_payment", "pts__member_deposit_payment.pmd_id = pts__member_deposit.pmd_id", "LEFT"],
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
            // if (isset($value['pm_metaState'])) $value['pm_metaState'] = json_decode($value['pm_metaState'], true);

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
            // if (isset($result['pm_metaState'])) $result['pm_metaState'] = json_decode($result['pm_metaState'], true);
        }
        return $result;
    }
}
