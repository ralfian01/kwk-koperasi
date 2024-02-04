<?php

namespace App\REST\V1\Account\Security\Password;

// use App\API\Library\MailSender;
use App\REST\V1\BaseRESTV1;

class Request extends BaseRESTV1
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
        // Make sure password valid
        if (!$this->dbRepo->validateAccountPassword($this->auth['uuid'], $this->payload['current_pass'])) {
            $this->setErrorStatus(401, [
                'description' => 'Invalid password'
            ]);
            return $this->error(...['code' => 401, 'report_id' => 'ASPR2']);
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

        if ($dbRepo->requestUpdateToken($token)) {

            // ### Send email confirmation to user email
            // $mailSender = new MailSender();
            // $mailSender
            //     ->setEmailTitle("Ngetes")
            //     ->setEmailBody("Haloo")
            //     ->setEmailDestination(
            //         $this->auth['username'],
            //         $this->auth['username']
            //     );
            // // ->send();

            // if (!$mailSender) {

            //     $this->setErrorStatus(500, [
            //         'description' => 'Failed to send email'
            //     ]);
            //     return $this->error(500);
            // }

            ### If update success
            if ($_ENV['ENVIRONMENT'] != 'production') {
                return $this->respond([
                    'token' => $token,
                ]);
            }

            return $this->respond([]);
        }

        ### If update fail
        return $this->error(500);
    }
}
