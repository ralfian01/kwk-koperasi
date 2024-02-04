<?php

namespace App\Models\View;

use MVCME\Models\DynModel;

class AccountView extends DynModel
{
    /*
     * ---------------------------------------------
     * SETUP DATABASE TABLE
     * ---------------------------------------------
     */

    protected $table = 'pts_account__vw';
    protected $returnType = 'array';


    /* NEW WRITING STANDARD */

    /*
     * ---------------------------------------------
     * SETUP SQL STANDARD
     * ---------------------------------------------
     */

    protected $columnAliases = [
        ["pav_id", "account_id"],
        ["pav_uuid", "uuid"],
        ["pav_username", "username"],
        ["pav_metaState", "pav_metaState"],
        ["pav_privilege", "pav_privilege"],
        ["pav_rolePrivilege", "pav_rolePrivilege"],
        [[
            "pav_privilege" => "pav_privilege",
            "pav_rolePrivilege" => "pav_rolePrivilege",
        ], "privilege"],
        [[
            "code" => "pav_roleCode",
            "name" => "pav_roleName",
        ], "role"],
    ];

    protected $filterData = [
        "account_id" => ["pav_id", "where"],
        "uuid" => ["pav_uuid", "where"],
        "username" => ["pav_username", "where"],
        "password" => ["pav_password", "where"],
        "actived" => ["pav_metaStatusActive", "where"],
        "deleted" => ["pav_metaStatusDelete", "where"],
        "deletable" => ["pav_metaDeletable", "where"],
        "role_code" => ["pav_roleCode", "whereIn"],
        "state_code" => ["pav_metaState.$.code", "whereInJson"],
        "state_value" => ["pav_metaState.$.value", "whereInJson"],
    ];

    protected $tableRelations = [];


    /*
     * ---------------------------------------------
     * RETURN DATA 
     * ---------------------------------------------
     */

    /**
     * Filters:
     * - account_id
     * - uuid
     * - username
     * - password
     * - actived
     * - deleted
     * - deletable
     * - role_code
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
            if (isset($value['role'])) $value['role'] = json_decode($value['role'], true);
            if (isset($value['pav_metaState'])) $value['pav_metaState'] = json_decode($value['pav_metaState'], true);

            if (isset($value['privilege'])) {
                $value['privilege'] = json_decode($value['privilege'], true);

                $value['privilege'] = array_merge(
                    json_decode($value['privilege']['pav_privilege'], true) ?? [],
                    json_decode($value['privilege']['pav_rolePrivilege'], true) ?? []
                );
            }

            $result[$key] = $value;
        }
        return $result;
    }

    /**
     * Filters:
     * - account_id
     * - uuid
     * 
     * Method to get 1 row specific data with more detailed data
     * @return array|null|object
     */
    public function data(?array $identifier = [])
    {
        // Override filter alias
        $filter = [
            "account_id" => ["pav_id", "where"],
            "uuid" => ["pav_uuid", "where"]
        ];

        $this
            ->setStdColumnAlias()
            ->setFilter($filter, $identifier)
            ->setStdTableRelation();

        $result = $this->get()->getResultArray();

        if ($result != null) {

            $result = $result[0];

            // Parse the column that has JSON value
            if (isset($result['role'])) $result['role'] = json_decode($result['role'], true);
            if (isset($result['pav_metaState'])) $result['pav_metaState'] = json_decode($result['pav_metaState'], true);

            if (isset($result['privilege'])) {
                $result['privilege'] = json_decode($result['privilege'], true);

                $result['privilege'] = array_merge(
                    json_decode($result['privilege']['pav_privilege'], true) ?? [],
                    json_decode($result['privilege']['pav_rolePrivilege'], true) ?? []
                );
            }
        }
        return $result;
    }
}
