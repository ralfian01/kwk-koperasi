<?php

namespace App\Models\Member;

use MVCME\Models\DynModel;

class Request extends DynModel
{
    /*
     * ---------------------------------------------
     * SETUP DATABASE TABLE
     * ---------------------------------------------
     */

    protected $table = 'pts__member_request';
    protected $primaryKey = 'pmr_id';
    protected $returnType = 'array';
    protected $allowedFields = [
        "pm_id", "pmr_type", "pmr_reason", "pmr_metaUpdatedAt", "pmr_metaUpdatedBy", "pmr_metaStatusActive"
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
        ["pmr_id", "request_id"],
        ["pmr_type", "request_type"],
        ["pmr_reason", "reason"],
        ["pmr_metaUpdatedAt", "metaUpdatedAt"],

        // 
        ["pts_account_updater.pa_username", "updater"]
    ];

    protected $filterData = [
        "uuid" => ["pts_account.pa_uuid", "where"],
        "account_id" => ["pts_account.pa_id", "where"],
        "member_id" => ["pm_id", "where"],
        "request_type" => ["pmr_type", "whereIn"],
        "updater_id" => ["pmr_metaUpdatedBy", "whereIn"],
        "actived" => ["pmr_metaStatusActive", "where"]
    ];

    protected $tableRelations = [
        ["pts_member", "pts_member.pm_id = pts__member_request.pm_id", "LEFT"],
        ["pts_account", "pts_account.pa_id = pts_member.pa_id", "LEFT"],
        ["pts_account pts_account_updater", "pts_account_updater.pa_id = pts__member_request.pmr_metaUpdatedBy", "LEFT"],
    ];


    /*
     * ---------------------------------------------
     * RETURN DATA 
     * ---------------------------------------------
     */

    /**
     * Filters:
     * - member_id
     * - request_type
     * - updater_id
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
        }
        return $result;
    }
}
