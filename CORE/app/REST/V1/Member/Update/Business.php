<?php

namespace App\REST\V1\Member\Update;

use App\REST\V1\BaseRESTV1;

class Business extends BaseRESTV1
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
        'registration_number' => [],
        'registration_type' => ['enum[NIB, SIUP, CV]'],
        'business_name' => ['maxlength[100]'],
        'business_address' => ['maxlength[355]'],
        'business_npwp' => [],
        'business_phone_number' => [],
        'business_email' => ['email'],
        'business_registration_date' => ['date_ymd'],
        'business_document' => ['single_file', 'file_accept[png, jpg, jpeg, pdf]'],
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
        // Make sure account is active
        if (!$this->dbRepo->checkAccountActive($this->auth['uuid'])) {
            return $this->error(401, [], 'MUB1');
        }

        // Make sure member is not deleted
        if (!$this->dbRepo->checkMemberDeleted($this->auth['uuid'])) {
            $this->setErrorStatus(
                403,
                ['description' => 'You have not yet registered to become a member']
            );
            return $this->error(403, [], 'MUB2');
        }

        return $this->update();
    }

    /** 
     * Function to update data 
     * @return object
     */
    public function update()
    {
        $dbRepo = new DBRepo($this->payload, $this->file, $this->auth);

        if ($dbRepo->updateMemberBusiness()) {

            ### If update success
            return $this->respond([]);
        }

        ### If update fail
        return $this->error(500);
    }
}
