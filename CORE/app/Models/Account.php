<?php

namespace App\Models;

use MVCME\Models\DynModel;

class Account extends DynModel
{
    /*
     * ---------------------------------------------
     * SETUP DATABASE TABLE
     * ---------------------------------------------
     */

    protected $table = 'pts_account';
    protected $primaryKey = 'pa_id';
    protected $returnType = 'array';
    protected $allowedFields = [
        "pa_uuid", "pa_username", "pa_password", "pr_id", "pa_metaUpdatedAt", "pa_metaDeletable", "pa_metaStatusActive", "pa_metaStatusDelete", "pa_metaState"
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
        ["pa_uuid", "uuid"],
        ["pa_username", "username"],
        ["pa_metaCreatedAt", "pa_metaCreatedAt"],
        ["pa_metaUpdatedAt", "pa_metaUpdatedAt"],
        ["pa_metaDeletable", "pa_metaDeletable"],
        ["pa_metaStatusActive", "pa_metaStatusActive"],
        ["pa_metaStatusDelete", "pa_metaStatusDelete"],
        ["pa_metaState", "pa_metaState"],
    ];

    protected $filterData = [
        "uuid" => ["pa_uuid", "where"],
        "username" => ["pa_username", "where"],
        "password" => ["pa_password", "where"],
        "actived" => ["pa_metaStatusActive", "where"],
        "deleted" => ["pa_metaStatusDelete", "where"],
        "deletable" => ["pa_metaDeletable", "where"],
        "state_code" => ["pa_metaState.$.code", "whereJson"],
        "state_value" => ["pa_metaState.$.value", "whereJson"],
        // 
        "keywords" => ["pa_username", "like"],
    ];

    protected $tableRelations = [];


    /*
     * ---------------------------------------------
     * RETURN DATA 
     * ---------------------------------------------
     */

    /**
     * Filters:
     * - uuid"
     * - username
     * - password
     * - actived
     * - deleted
     * - deletable
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
            if (isset($value['pa_metaState'])) $value['pa_metaState'] = json_decode($value['pa_metaState'], true);

            $result[$key] = $value;
        }
        return $result;
    }

    /**
     * Filters:
     * - account_id
     * = uuid
     * 
     * Method to get 1 row specific data with more detailed data
     * @return array|null|object
     */
    public function data(?array $identifier = [])
    {
        // Override filter alias
        $filter = [
            "account_id" => ["pa_id", "where"],
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
            if (isset($result['pa_metaState'])) $result['pa_metaState'] = json_decode($result['pa_metaState'], true);
        }
        return $result;
    }
}
