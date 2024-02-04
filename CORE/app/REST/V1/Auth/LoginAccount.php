<?php

namespace App\REST\V1\Auth;

use App\REST\V1\BaseRESTV1;
use App\Models\Account;
use AppConfig\App;
use AppConfig\TopSecret;
use Firebase\JWT\JWT;

class LoginAccount extends BaseRESTV1
{
    private $tempAuth;

    public function __construct(
        ?array $payload = [],
        ?array $file = [],
        ?array $auth = []
        // ?DBRepo $dbRepo = null
    ) {

        $this->payload = $payload;
        $this->file = $file;
        $this->auth = $auth;
        // $this->dbRepo = $dbRepo ?? new DBRepo();
        return $this;
    }

    /* Edit line below to set payload rules */
    protected $payloadRules = [];


    /**
     * Function to validate username/email and password
     * @return bool
     */
    public function validateAccount()
    {
        // Get Request user and pass from request header
        $user = $this->request->getServer('PHP_AUTH_USER');
        $pass = $this->request->getServer('PHP_AUTH_PW');

        // Check username/email and password
        $dbAcc = new Account();
        $data = $dbAcc
            ->all([
                'username' => $user,
                'password' => hash('SHA256', $pass),
                'deleted' => false
            ]);

        if ($data == null)
            return false;

        $this->tempAuth = $data[0];
        return true;
    }

    /**
     * Function to validate username/email and password
     * @return bool
     */
    public function checkAccountActive()
    {
        return $this->tempAuth['pa_metaStatusActive'] == 1;
    }

    /**
     * Function to check account state
     * @return bool
     */
    public function checkAccountState(&$error)
    {
        // Check meta state
        if (!isset($this->tempAuth['pa_metaState']['code'])) {
            return true;
        }

        switch ($this->tempAuth['pa_metaState']['code']) {

            case 'CONFIRM_EMAIL':
                $error = (object) [
                    'code' => 403,
                    'error_detail' => [
                        'reason' => 'Email has not been verified',
                    ],
                    'report_id' => 'ALA3'
                ];
                return false;
                break;
        }
    }


    /*
     * ---------------------------------------------
     * MAIN ACTIVITY
     * ---------------------------------------------
     */

    protected function mainActivity()
    {
        return $this->nextValidation();
    }

    /**
     * Handle the next step of payload validation
     * @return void
     */
    private function nextValidation()
    {
        // Validate account username/email and password
        if (!$this->validateAccount()) {
            return $this->error(...['code' => 401, 'report_id' => 'ALA1']);
        }

        // Make sure account is active
        if (!$this->checkAccountActive()) {
            $this->setErrorStatus(401, [
                'description' => 'Your account is suspended'
            ]);
            return $this->error(...['code' => 401, 'report_id' => 'ALA2']);
        }

        // Make sure account does not have forbidden state
        if (!$this->checkAccountState($error)) {
            return $this->error(
                $error->code,
                $error->error_detail,
                $error->report_id
            );
        }

        return $this->getJWT();
    }

    /**
     * Function to get JWT Token
     * @param array $data
     * @return array
     */
    public function getJWT()
    {
        $config = new App;
        $secret = new TopSecret();

        // Start create JWT Token
        $reqTime = time();
        $expTime = $reqTime + (3600 * 24); // 1 Hour * 24: Expires in 24 hours
        $jsonJWT = [
            'iss' => 'Putsutech JWT Authentication',
            'iat' => $reqTime,
            'exp' => $expTime,
            'host' => $config->hostname,
            'uid_b64' => base64_encode($this->tempAuth['uuid']),
            'username' => $this->tempAuth['username'],
            'ua' => base64_encode($_SERVER['HTTP_USER_AGENT']),
        ];

        $response = [
            'token' => JWT::encode($jsonJWT, $secret->origin('bearer_key'), 'HS256'),
        ];

        return $this->respond($response);
    }
}
