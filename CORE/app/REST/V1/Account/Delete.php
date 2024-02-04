<?php

namespace App\REST\V1\Account;

use App\REST\V1\BaseRESTV1;

class Delete extends BaseRESTV1
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
        // Make sure password valid
        if (!$this->dbRepo->validateAccountPassword($this->auth['uuid'], $this->payload['password'])) {
            return $this->error(401, [[
                'payload_name' => 'password',
                'reason' => 'Invalid password'
            ]], 'AD2');
        }

        return $this->delete();
    }

    /** 
     * Function to delete data
     * @return object
     */
    public function delete()
    {
        $dbRepo = new DBRepo($this->payload, $this->file, $this->auth);

        if ($dbRepo->deleteData()) {

            ### If update success
            return $this->respond([]);
        }

        ### If update fail
        return $this->error(500);
    }
}
