<?php

namespace App\Models\Member;

use MVCME\Models\DynModel;

class Member extends DynModel
{
    /*
     * ---------------------------------------------
     * SETUP DATABASE TABLE
     * ---------------------------------------------
     */

    protected $table = 'pts_member';
    protected $primaryKey = 'pm_id';
    protected $returnType = 'array';
    protected $allowedFields = [
        "pa_id", "pm_registerNumber", "pm_nickname", "pm_addressDomicile", "pm_phoneNumber", "pm_waNumber", "pm_metaUpdatedAt", "pm_metaStatusActive", "pm_metaStatusDelete", "pm_metaState", "pm_verifiedBy"
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
        ["pa_id", "account_id"],
        ["pm_id", "member_id"],
        ["pm_registerNumber", "member_register_number"],
        ["pm_nickname", "nickname"],
        ["pm_addressDomicile", "address_domicile"],
        ["pm_phoneNumber", "phone_number"],
        ["pm_waNumber", "wa_number"],
        ["pm_metaState", "pm_metaState"],

        // 
        ["pts_account.pa_username", "username"],
        ["pts_account.pa_uuid", "uuid"],

        // 
        ["pts_account_verifier.pa_username", "verified_by"],
    ];

    protected $filterData = [
        "account_id" => ["pa_id", "where"],
        "uuid" => ["pts_account.pa_uuid", "where"],
        "member_id" => ["pm_id", "where"],
        "member_register_number" => ["pm_registerNumber", "like"],
        "nickname" => ["pm_nickname", "like"],
        "phone_number" => ["pm_phoneNumber", "where"],
        "wa_number" => ["pm_waNumber", "where"],
        "actived" => ["pm_metaStatusActive", "where"],
        "deleted" => ["pm_metaStatusDelete", "where"],
        "state_code" => ["pm_metaState.$.code", "whereInJson"],
        "state_value" => ["pm_metaState.$.value", "whereInJson"],
    ];

    protected $tableRelations = [
        ["pts_account", "pts_account.pa_id = pts_member.pa_id", "LEFT"],
        ["pts_account pts_account_verifier", "pts_account_verifier.pa_id = pts_member.pm_verifiedBy", "LEFT"],
    ];


    /*
     * ---------------------------------------------
     * RETURN DATA 
     * ---------------------------------------------
     */

    /**
     * Filters:
     * - account_id
     * - uuid
     * - member_id
     * - nickname
     * - phone_number
     * - wa_number
     * - actived
     * - deleted
     * - state_code
     * - state_value
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

            $result[$key] = $value;
        }
        return $result;
    }

    /**
     * Filters:
     * - member_id
     * - uuid
     * 
     * Method to get 1 row specific data with more detailed data
     * @return array|null|object
     */
    public function data(?array $identifier = [])
    {
        // Override filter alias
        $filter = [
            "member_id" => ["pm_id", "where"],
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
            if (isset($result['pm_metaState'])) $result['pm_metaState'] = json_decode($result['pm_metaState'], true);
        }
        return $result;
    }
}
