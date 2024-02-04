<?php

namespace App\REST\V1\Member\Delete;

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
        "reason" => ['required', "maxlength[500]"]
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
        // Make sure member is not deleted
        if (!$this->dbRepo->checkMemberDeleted($this->auth['uuid'])) {
            $this->setErrorStatus(403, [
                'description' => 'Your member data already deleted'
            ]);
            return $this->error(...['code' => 403, 'report_id' => 'MDR1']);
        }

        // Make sure member dont have active request
        if (!$this->dbRepo->checkMemberRequest($this->auth['uuid'])) {
            $this->setErrorStatus(409, [
                'description' => 'You still have active requests'
            ]);
            return $this->error(...['code' => 409, 'report_id' => 'MDR2']);
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

        if ($dbRepo->requestDelete()) {

            ### If update success
            return $this->respond([]);
        }

        ### If update fail
        return $this->error(500);
    }
}
