<?php

namespace App\REST\V1\Registration\Member;

use App\REST\V1\BaseRESTV1;

class Insert extends BaseRESTV1
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
        // Member Data
        'nickname' => ['maxlength[100]'],
        'address_domicile' => ['required', 'maxlength[355]'],
        'phone_number' => ['required', 'call_number[62]'],
        'wa_number' => ['required', 'call_number[62]'],

        // Identity
        'nik' => ['required'],
        'fullname' => ['required', 'maxlength[100]'],
        'birth_place' => ['required'],
        'birth_date' => ['required', 'date_ymd'],
        'gender' => ['required', 'enum[M, F]'],
        'address' => ['required', 'maxlength[355]'],
        'npwp' => ['required'],
        'photo_id_card' => ['required', 'single_file', 'file_accept[png, jpg, jpeg, pdf]'],

        // Business
        'registration_number' => ['required'],
        'registration_type' => ['required', 'enum[NIB, SIUP, CV]'],
        'business_name' => ['required', 'maxlength[100]'],
        'business_address' => ['required', 'maxlength[355]'],
        'business_npwp' => ['required'],
        'business_phone_number' => ['required', 'call_number[62]'],
        'business_email' => ['required', 'email'],
        'business_registration_date' => ['required', 'date_ymd'],
        'business_document' => ['required', 'single_file', 'file_accept[png, jpg, jpeg, pdf]'],
    ];

    /* Edit this line to set privilege rules */
    protected $privilegeRules = [];


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
        // Make sure the account only has one member data 
        if (!$this->dbRepo->checkMemberRegistered($this->auth['uuid'])) {
            $this->setErrorStatus(409, [
                'description' => 'You already have a member account'
            ]);
            return $this->error(...['code' => 409, 'report_id' => 'RMI1']);
        }

        return $this->insert();
    }

    /** 
     * Function to insert new data 
     * @return object
     */
    public function insert()
    {
        $dbRepo = new DBRepo($this->payload, $this->file, $this->auth);

        if ($dbRepo->insertData()) {

            // ### Send notification via Whatsapp API
            // // Get a list of secretary accounts
            // $secretaryList = (new Account())
            //     ->all([
            //         'role' => 'SEKRE',
            //         'actived' => true,
            //         'deleted' => false
            //     ]);

            // $numberList = [];
            // foreach ($secretaryList as $list) {
            //     $numberList[] = $list['phone_number'];
            // }
            // $numberList = implode(',', $numberList);

            // $message = "*ANGGOTA JEMAAT BARU*
            // \nCalon anggota jemaat baru membutuhkan konfirmasi data segera.
            // \nRingkasan Data Anggota: \nNama: *{$this->payload['name']}*
            // ";

            // $sendWa = $this->sendWA($numberList, $message);

            // if (!$sendWa) {
            //     print_r($sendWa);
            //     return $this->error(500);
            // }

            ### If update success
            return $this->respond([]);
        }

        ### If update fail
        return $this->error(500);
    }
}
