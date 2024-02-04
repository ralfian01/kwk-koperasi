<?php

namespace App\REST\V1\Registration\Account;

// use App\API\Library\MailSender;
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
        'email' => ['required', 'email'],
        'password' => ['required'],
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
        // Make sure email available
        if (!$this->dbRepo->checkUsernameUsage($this->payload['email'])) {
            $this->setErrorStatus(409, [
                'description' => 'Username or Email already used'
            ]);
            return $this->error(...['code' => 409, 'report_id' => 'RAI1']);
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

            // ### Send email confirmation to user email
            // $mailSender = new MailSender();
            // $mailSender
            //     ->setEmailTitle("Ngetes")
            //     ->setEmailBody("Haloo")
            //     ->setEmailDestination(
            //         $this->payload['email'],
            //         $this->payload['email']
            //     );
            // // ->send();

            // if (!$mailSender) {

            //     $this->setErrorStatus(500, [
            //         'description' => 'Failed to send email'
            //     ]);
            //     return $this->error(500);
            // }

            ### If update success
            return $this->respond([]);
        }

        ### If update fail
        return $this->error(500);
    }
}
