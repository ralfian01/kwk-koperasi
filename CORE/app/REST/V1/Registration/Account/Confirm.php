<?php

namespace App\REST\V1\Registration\Account;

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
        'utm_code' => ['required']
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
        // Make sure account has valid state code
        if (!$this->dbRepo->validateAccountState($this->payload['utm_code'], $this->auth)) {
            $this->setErrorStatus(403, [
                'description' => 'Invalid request token'
            ]);
            return $this->error(...['code' => 403, 'report_id' => 'RAC1']);
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

        if ($dbRepo->confirmRegistration($this->payload, $this->file)) {

            ### If update success
            return $this->respond([]);
        }

        ### If update fail
        return $this->error(500);
    }
}
