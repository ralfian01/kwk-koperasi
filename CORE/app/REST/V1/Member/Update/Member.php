<?php

namespace App\REST\V1\Member\Update;

use App\REST\V1\BaseRESTV1;

class Member extends BaseRESTV1
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
        'nickname' => ['maxlength[100]'],
        'address_domicile' => ['maxlength[355]'],
        'phone_number' => ['call_number[62]'],
        'wa_number' => ['call_number[62]'],
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
            return $this->error(401, [], 'MUM1');
        }

        // Make sure member is not deleted
        if (!$this->dbRepo->checkMemberDeleted($this->auth['uuid'])) {
            $this->setErrorStatus(
                403,
                ['description' => 'You have not yet registered to become a member']
            );
            return $this->error(403, [], 'MUM2');
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

        if ($dbRepo->updateMemberData()) {

            ### If update success
            return $this->respond([]);
        }

        ### If update fail
        return $this->error(500);
    }
}
