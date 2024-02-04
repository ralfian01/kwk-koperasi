<?php

namespace App\Models\View;

use MVCME\Models\DynModel;

class RoleView extends DynModel
{
    /*
     * ---------------------------------------------
     * SETUP DATABASE TABLE
     * ---------------------------------------------
     */

    protected $table = 'pts_role__vw';
    protected $returnType = 'array';


    /* NEW WRITING STANDARD */

    /*
     * ---------------------------------------------
     * SETUP SQL STANDARD
     * ---------------------------------------------
     */

    protected $columnAliases = [
        ["prv_id", "role_id"],
        ["prv_code", "role_code"],
        ["prv_name", "role_name"],
        ["prv_privilege", "role_privilege"]
    ];

    protected $filterData = [
        "role_id" => ["prv_id", "where"],
        "role_code" => ["prv_code", "whereIn"],
        "role_name" => ["prv_code", "like"]
    ];

    protected $tableRelations = [];


    /*
     * ---------------------------------------------
     * RETURN DATA 
     * ---------------------------------------------
     */

    /**
     * Filters:
     * - role_id
     * - role_code
     * - role_name
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
            if (isset($value['prv_privilege'])) $value['prv_privilege'] = json_decode($value['prv_privilege'], true);

            $result[$key] = $value;
        }
        return $result;
    }

    /**
     * Filters:
     * - role_id
     * - role_code
     * 
     * Method to get 1 row specific data with more detailed data
     * @return array|null|object
     */
    public function data(?array $identifier = [])
    {
        // Override filter alias
        $filter = [
            "role_id" => ["prv_id", "where"],
            "role_code" => ["prv_code", "where"]
        ];

        $this
            ->setStdColumnAlias()
            ->setFilter($filter, $identifier)
            ->setStdTableRelation();

        $result = $this->get()->getResultArray();

        if ($result != null) {

            $result = $result[0];

            // Parse the column that has JSON value
            if (isset($result['prv_privilege'])) $result['prv_privilege'] = json_decode($result['prv_privilege'], true);
        }
        return $result;
    }
}
