<?php

namespace App\REST\V1\Manage\Member;

use App\REST\V1\BaseRESTV1;

class ManualUpdate extends BaseRESTV1
{
    public function __construct(
        ?array $payload = [],
        ?array $file = [],
        ?array $auth = [],
        ?DBRepo $dbRepo = null
    ) {

        $this->payload = $payload;
        $this->file = $file;
        $this->auth = $auth;
        $this->dbRepo = $dbRepo ?? new DBRepo();
        return $this;
    }

    /* Edit this line to set payload rules */
    protected $payloadRules = [
        'id' => ['required', 'base64'],
        'register_number' => [],
        'nickname' => ['maxlength[100]'],
        'address_domicile' => ['maxlength[355]'],
        'phone_number' => ['call_number[62]'],
        'wa_number' => ['call_number[62]'],

        // Identity
        'nik' => [],
        'fullname' => ['maxlength[100]'],
        'birth_place' => [],
        'birth_date' => ['date_ymd'],
        'gender' => ['enum[M, F]'],
        'address' => ['maxlength[355]'],
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
    ];

    /* Edit this line to set privilege rules */
    protected $privilegeRules = [
        'MEMBER_MANAGE_VIEW',
        'MEMBER_MANAGE_UPDATE',
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
        return $this->update();
    }

    /** 
     * Function to update data 
     * @return object
     */
    public function update()
    {
        $dbRepo = new DBRepoManual($this->payload, $this->file, $this->auth);

        if ($dbRepo->manualUpdate()) {

            ### If update success
            return $this->respond([]);
        }

        ### If update fail
        return $this->error(500);
    }
}
