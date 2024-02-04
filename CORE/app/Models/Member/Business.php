<?php

namespace App\Models\Member;

use MVCME\Models\DynModel;

class Business extends DynModel
{
    /*
     * ---------------------------------------------
     * SETUP DATABASE TABLE
     * ---------------------------------------------
     */

    protected $table = 'pts_member__business';
    protected $primaryKey = 'pmb_id';
    protected $returnType = 'array';
    protected $allowedFields = [
        "pm_id", "pmb_registrationNumber", "pmb_registrationType", "pmb_businessName", "pmb_businessAddress", "pmb_businessNPWP", "pmb_businessPhoneNumber", "pmb_businessEmail", "pmb_businessRegistrationDate", "pmb_businessDocument"
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
        ["pmb_registrationNumber", "registration_number"],
        ["pmb_registrationType", "registration_type"],
        ["pmb_businessName", "business_name"],
        ["pmb_businessAddress", "business_address"],
        ["pmb_businessNPWP", "business_npwp"],
        ["pmb_businessPhoneNumber", "business_phone_number"],
        ["pmb_businessEmail", "business_email"],
        ["pmb_businessRegistrationDate", "registration_date"],
        ["pmb_businessDocument", "business_document"],
    ];

    protected $filterData = [
        "member_id" => ["pm_id", "where"],
        "registration_number" => ["pmb_registrationNumber", "where"],
        "registration_type" => ["pmb_registrationType", "whereIn"],
        "business_name" => ["pmb_businessName", "like"],
        "business_npwp" => ["pmb_businessNPWP", "where"],
        "business_phone_number" => ["pmb_businessPhoneNumber", "like"],
        "business_email" => ["pmb_businessEmail", "like"],
        "registration_date" => ["pmb_businessRegistrationDate", "where"],
    ];

    protected $tableRelations = [
        ["pts_member", "pts_member.pm_id = pts_member__business.pm_id", "LEFT"],
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
