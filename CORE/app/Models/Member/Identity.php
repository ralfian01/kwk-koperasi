<?php

namespace App\Models\Member;

use MVCME\Models\DynModel;

class Identity extends DynModel
{
    /*
     * ---------------------------------------------
     * SETUP DATABASE TABLE
     * ---------------------------------------------
     */

    protected $table = 'pts_member__identity';
    protected $primaryKey = 'pmi_id';
    protected $returnType = 'array';
    protected $allowedFields = [
        "pm_id", "pmi_nik", "pmi_fullname", "pmi_birthPlace", "pmi_birthDate", "pmi_gender", "pmi_address", "pmi_npwp", "pmi_photoIdCard"
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
        ["pmi_id", "pmi_id"],
        ["pmi_nik", "nik"],
        ["pmi_fullname", "fullname"],
        ["pmi_birthPlace", "birth_place"],
        ["pmi_birthDate", "birth_date"],
        ["pmi_gender", "gender"],
        ["pmi_address", "address"],
        ["pmi_npwp", "npwp"],
        ["pmi_photoIdCard", "photo_id_card"],
    ];

    protected $filterData = [
        "member_id" => ["pm_id", "where"],
        "fullname" => ["pmi_fullname", "like"],
        "gender" => ["pmi_gender", "whereIn"],
        "npwp" => ["pmi_npwp", "where"],
        "nik" => ["pmi_nik", "where"],
    ];

    protected $tableRelations = [
        ["pts_member", "pts_member.pm_id = pts_member__identity.pm_id", "LEFT"],
    ];


    /*
     * ---------------------------------------------
     * RETURN DATA 
     * ---------------------------------------------
     */

    /**
     * Filters:
     * - member_id
     * - fullname
     * - gender
     * - npwp
     * - nik
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
            // if (isset($result['pm_metaState'])) $result['pm_metaState'] = json_decode($result['pm_metaState'], true);
        }
        return $result;
    }
}
