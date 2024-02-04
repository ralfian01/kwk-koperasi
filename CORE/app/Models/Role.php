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

    protected $table = 'pts_role';
    protected $primaryKey = 'pr_id';
    protected $returnType = 'array';


    /* NEW WRITING STANDARD */

    /*
     * ---------------------------------------------
     * SETUP SQL STANDARD
     * ---------------------------------------------
     */

    protected $columnAliases = [
        ["pr_role", "role_id"],
        ["pr_code", "role_code"],
        ["pr_description", "role_description"],


        ["pa_uuid", "uuid"],
        ["pa_username", "username"],
        ["pa_privilege", "pa_privilege"],
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
        "role" => ["pts_role_account.pra_id", "whereIn"],
        "state_code" => ["pa_metaState.$.code", "whereJson"],
        "state_value" => ["pa_metaState.$.value", "whereJson"],
    ];

    protected $tableRelations = [
        ["pts_role_account", "pts_role_account.pra_id = pts_account.pra_id", "LEFT"],
    ];


    /*
     * ---------------------------------------------
     * RETURN DATA 
     * ---------------------------------------------
     */

    /**
     * Filters:
     * - uuid
     * - username
     * - password
     * - actived
     * - deleted
     * - deletable
     * - role
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
            if (isset($value['pa_privilege'])) $value['pa_privilege'] = json_decode($value['pa_privilege'], true);
            if (isset($value['pra_privilege'])) $value['pra_privilege'] = json_decode($value['pra_privilege'], true);

            $value['privilege'] = array_merge($value['pa_privilege'] ?? [], $value['pra_privilege'] ?? []);

            $result[$key] = $value;
        }
        return $result;
    }

    /**
     * Filters:
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
            if (isset($result['pa_metaState'])) $result['pa_metaState'] = json_decode($result['pa_metaState'], true);
            if (isset($result['pa_privilege'])) $result['pa_privilege'] = json_decode($result['pa_privilege'], true);
            if (isset($result['pra_privilege'])) $result['pra_privilege'] = json_decode($result['pra_privilege'], true);

            $result['privilege'] = array_merge($result['pa_privilege'] ?? [], $result['pra_privilege'] ?? []);
        }
        return $result;
    }
}
