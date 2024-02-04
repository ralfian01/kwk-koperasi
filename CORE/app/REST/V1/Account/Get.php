<?php

namespace App\REST\V1\Account;

use App\REST\V1\BaseRESTV1;

class Get extends BaseRESTV1
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
        'uuid' => ['base64'],
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

        return $this->get();
    }

    /** 
     * Function to get data
     * @return object
     */
    public function get()
    {
        $dbRepo = new DBRepo($this->payload, $this->file, $this->auth);

        $data = $dbRepo->getData();

        ### If id not found
        if ($data == null) {
            $this->setErrorStatus(404, ['description' => 'Data Not Found']);
            return $this->error(404);
        }

        return $this->respond($data);
    }
}
