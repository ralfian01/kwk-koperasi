<?php

namespace App\Models\Member;

use MVCME\Models\DynModel;

class MemberView extends DynModel
{
    /*
     * ---------------------------------------------
     * SETUP DATABASE TABLE
     * ---------------------------------------------
     */

    protected $table = 'pts_member';
    protected $returnType = 'array';


    /* NEW WRITING STANDARD */

    /*
     * ---------------------------------------------
     * SETUP SQL STANDARD
     * ---------------------------------------------
     */

    protected $columnAliases = [
        ["pa_id", "account_id"],
        ["pm_id", "member_id"],
        ["pm_nickname", "nickname"],
        ["pm_addressDomicile", "address_domicile"],
        ["pm_phoneNumber", "phone_number"],
        ["pm_waNumber", "wa_number"],
        ["pm_metaState", "pm_metaState"],
        ["pm_metaStatusActive", "actived"],
        ["pm_metaCreatedAt", "register_date"],
        ["pm_registerNumber", "register_number"],

        // Identity
        [[
            "nik" => "pts_member__identity.pmi_nik",
            "fullname" => "pts_member__identity.pmi_fullname",
            "birth_place" => "pts_member__identity.pmi_birthPlace",
            "birth_date" => "pts_member__identity.pmi_birthDate",
            "gender" => "pts_member__identity.pmi_gender",
            "address" => "pts_member__identity.pmi_address",
            "npwp" => "pts_member__identity.pmi_npwp",
            "photo_id_card" => "pts_member__identity.pmi_photoIdCard",
        ], "identity"],

        // Business
        [[
            "registration_number" => "pts_member__business.pmb_registrationNumber",
            "registration_type" => "pts_member__business.pmb_registrationType",
            "business_name" => "pts_member__business.pmb_businessName",
            "business_address" => "pts_member__business.pmb_businessAddress",
            "business_npwp" => "pts_member__business.pmb_businessNPWP",
            "business_phone_number" => "pts_member__business.pmb_businessPhoneNumber",
            "business_email" => "pts_member__business.pmb_businessEmail",
            "registration_date" => "pts_member__business.pmb_businessRegistrationDate",
            "business_document" => "pts_member__business.pmb_businessDocument",
        ], "business"],
        // 
        ["pts_account.pa_username", "username"],
        // 
        ["pts_account_verifier.pa_username", "verified_by"],
        // 
        [[
            "member_id" => "pts_member_verifier.pm_id",
            "register_number" => "pts_member_verifier.pm_registerNumber",
            "fullname" => "pts_member__identity_verifier.pmi_fullname",
        ], "verifier"],
    ];

    protected $filterData = [
        "account_id" => ["pa_id", "where"],
        "uuid" => ["pts_account.pa_uuid", "where"],
        "role_code" => ["pts_role.pr_code", "whereIn"],
        // 
        "member_id" => ["pm_id", "where"],
        "member_register_number" => ["pm_registerNumber", "like"],
        "nickname" => ["pm_nickname", "like"],
        "phone_number" => ["pm_phoneNumber", "like"],
        "wa_number" => ["pm_waNumber", "like"],
        "state_code" => ["pm_metaState.$.code", "whereInJson"],
        "state_value" => ["pm_metaState.$.value", "whereInJson"],
        "actived" => ["pm_metaStatusActive", "whereIn"],
        "deleted" => ["pm_metaStatusDelete", "whereIn"],
        "verified_by" => ["pm_verifiedBy", "where"],
        // 
        "fullname" => ["pts_member__identity.pmi_fullname", "like"],
        "gender" => ["pts_member__identity.pmi_gender", "whereIn"],
        "npwp" => ["pts_member__identity.pmi_npwp", "where"],
        "nik" => ["pts_member__identity.pmi_nik", "where"],
        // 
        "registration_number" => ["pts_member__business.pmb_registrationNumber", "where"],
        "registration_type" => ["pts_member__business.pmb_registrationType", "whereIn"],
        "business_name" => ["pts_member__business.pmb_businessName", "like"],
        "business_npwp" => ["pts_member__business.pmb_businessNPWP", "where"],
        "business_phone_number" => ["pts_member__business.pmb_businessPhoneNumber", "like"],
        "business_email" => ["pts_member__business.pmb_businessEmail", "like"],
        "registration_date" => ["pts_member__business.pmb_businessRegistrationDate", "where"],
        // 
        "keywords" => ["pm_registerNumber || pm_nickname || pm_phoneNumber || pm_waNumber || pts_member__identity.pmi_fullname", "like"],
    ];

    protected $tableRelations = [
        ["pts_account", "pts_account.pa_id = pts_member.pa_id", "LEFT"],
        ["pts_role", "pts_role.pr_id = pts_account.pr_id", "LEFT"],
        ["pts_member__identity", "pts_member__identity.pm_id = pts_member.pm_id", "LEFT"],
        ["pts_member__business", "pts_member__business.pm_id = pts_member.pm_id", "LEFT"],
        // Verifier
        ["pts_account pts_account_verifier", "pts_account_verifier.pa_id = pts_member.pm_verifiedBy", "LEFT"],
        ["pts_member pts_member_verifier", "pts_member_verifier.pa_id = pts_account_verifier.pa_id", "LEFT"],
        ["pts_member__identity pts_member__identity_verifier", "pts_member__identity_verifier.pm_id = pts_member_verifier.pm_id", "LEFT"],
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
     * - verified_by
     * - state_code
     * - state_value
     * - fullname
     * - gender
     * - npwp
     * - nik
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
            if (isset($value['identity'])) $value['identity'] = json_decode($value['identity'], true);
            if (isset($value['business'])) $value['business'] = json_decode($value['business'], true);
            if (isset($value['verifier'])) $value['verifier'] = json_decode($value['verifier'], true);

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
            if (isset($result['identity'])) $result['identity'] = json_decode($result['identity'], true);
            if (isset($result['business'])) $result['business'] = json_decode($result['business'], true);
            if (isset($result['verifier'])) $result['verifier'] = json_decode($result['verifier'], true);
        }
        return $result;
    }
}
