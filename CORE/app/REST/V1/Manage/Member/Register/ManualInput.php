<?php

namespace App\REST\V1\Manage\Member\Register;

use App\REST\V1\BaseRESTV1;

class ManualInput extends BaseRESTV1
{
    public function __construct(
        ?array $payload = [],
        ?array $file = [],
        ?array $auth = [],
        ?DBRepoManual $dbRepo = null
    ) {

        $this->payload = $payload;
        $this->file = $file;
        $this->auth = $auth;
        $this->dbRepo = $dbRepo ?? new DBRepoManual();
        return $this;
    }

    /* Edit this line to set payload rules */
    protected $payloadRules = [
        // Member Data
        'register_number' => ['required', 'maxlength[100]'],
        'nickname' => ['maxlength[100]'],
        'address_domicile' => ['required', 'maxlength[355]'],
        'phone_number' => ['required', 'call_number[62]'],
        'wa_number' => ['call_number[62]'],

        // Identity
        'nik' => ['required'],
        'fullname' => ['required', 'maxlength[100]'],
        'birth_place' => ['required'],
        'birth_date' => ['required', 'date_ymd'],
        'gender' => ['required', 'enum[M, F]'],
        'address' => ['required', 'maxlength[355]'],
        'npwp' => [],

        // Business
        'registration_number' => [],
        'registration_type' => ['enum[NIB, SIUP, CV]'],
        'business_name' => ['maxlength[100]'],
        'business_address' => ['maxlength[355]'],
        'business_npwp' => [],
        'business_phone_number' => ['call_number[62]'],
        'business_email' => ['email'],
        'business_registration_date' => ['date_ymd'],
        'business_legal' => [],
    ];

    /* Edit this line to set privilege rules */
    protected $privilegeRules = [
        'MEMBER_MANAGE_VIEW',
        'MEMBER_MANAGE_INSERT'
    ];


    protected function mainActivity($id = null)
    {
        return $this->nextValidation();
    }

    /**
     * Handle the next step of payload validation
     * @return void
     */
    private function nextValidation()
    {
        return $this->insert();
    }

    /** 
     * Function to insert data 
     * @return object
     */
    public function insert()
    {
        $dbRepo = new DBRepoManual($this->payload, $this->file, $this->auth);

        if ($dbRepo->manualInput()) {

            ### If update success
            return $this->respond([]);
        }

        ### If update fail
        return $this->error(500);
    }
}
