<?php

namespace App\REST\V1\Account\Security\Password;

// use App\API\Library\MailSender;
use App\REST\V1\BaseRESTV1;

class Confirm extends BaseRESTV1
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
        'current_pass' => ['required'],
        'new_pass' => ['required'],
        'token' => ['required']
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
        // Make sure account has valid state
        if (!$this->dbRepo->validateAccountState($this->auth['uuid'])) {
            return $this->error(...['code' => 401, 'report_id' => 'ASPC2']);
        }

        // Make sure password valid
        if (!$this->dbRepo->validateAccountPassword($this->auth['uuid'], $this->payload['current_pass'])) {
            $this->setErrorStatus(401, [
                'description' => 'Invalid current password'
            ]);
            return $this->error(...['code' => 401, 'report_id' => 'ASPC3']);
        }

        // Make sure client insert valid token and new password
        if (!$this->dbRepo->validatePassToken($this->auth['uuid'], $this->payload['token'], $this->payload['new_pass'])) {
            $this->setErrorStatus(401, [
                'description' => 'Invalid token or the new_pass does not match the previous request'
            ]);
            return $this->error(...['code' => 401, 'report_id' => 'ASPC4']);
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

        if ($dbRepo->updatePassword()) {

            ### If update success
            return $this->respond([]);
        }

        ### If update fail
        return $this->error(500);
    }
}
