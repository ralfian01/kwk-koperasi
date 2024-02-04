<?php

namespace App\Models;

use MVCME\Models\DynModel;

class Deposit extends DynModel
{
    /*
     * ---------------------------------------------
     * SETUP DATABASE TABLE
     * ---------------------------------------------
     */

    protected $table = 'pts_deposit';
    protected $returnType = 'array';


    /* NEW WRITING STANDARD */

    /*
     * ---------------------------------------------
     * SETUP SQL STANDARD
     * ---------------------------------------------
     */

    protected $columnAliases = [
        ["pd_id", "deposit_id"],
        ["pd_code", "deposit_code"],
        ["pd_name", "deposit_name"],
        ["pd_amount", "deposit_amount"],
    ];

    protected $filterData = [
        "deposit_id" => ["pd_id", "where"],
        "deposit_code" => ["pd_code", "whereIn"],
        "deposit_name" => ["pd_name", "like"],
    ];

    protected $tableRelations = [];


    /*
     * ---------------------------------------------
     * RETURN DATA 
     * ---------------------------------------------
     */

    /**
     * Filters:
     * - deposit_id
     * - deposit_code
     * - deposit_name
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
            // if (isset($value['prv_privilege'])) $value['prv_privilege'] = json_decode($value['prv_privilege'], true);

            $result[$key] = $value;
        }
        return $result;
    }

    /**
     * Filters:
     * - deposit_id
     * - deposit_code
     * 
     * Method to get 1 row specific data with more detailed data
     * @return array|null|object
     */
    public function data(?array $identifier = [])
    {
        // Override filter alias
        $filter = [
            "deposit_id" => ["pd_id", "where"],
            "deposit_code" => ["pd_code", "where"]
        ];

        $this
            ->setStdColumnAlias()
            ->setFilter($filter, $identifier)
            ->setStdTableRelation();

        $result = $this->get()->getResultArray();

        if ($result != null) {

            $result = $result[0];

            // Parse the column that has JSON value
            // if (isset($result['prv_privilege'])) $result['prv_privilege'] = json_decode($result['prv_privilege'], true);
        }
        return $result;
    }
}
