<?php

namespace App\REST\V1\Member\Deposit;

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
        'deposit_type' => ['required', 'enum[BASE, MANDATORY, VOLUNTARY]'],
        'amount' => ['int'],
    ];

    /* Edit this line to set privilege rules */
    protected $privilegeRules = [
        'MEMBER_DEPOSIT_VIEW',
        'MEMBER_DEPOSIT_ADD'
    ];


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
        // Make sure member is active
        if (!$this->dbRepo->checkMemberActive($this->auth['uuid'])) {
            $this->setErrorStatus(403, [
                'description' => 'You do not have active member data'
            ]);
            return $this->error(...['code' => 403, 'report_id' => 'MDR1']);
        }

        // Make sure client input valid deposit code
        if (!$this->dbRepo->checkDepositCode($this->payload['deposit_type'])) {
            $this->setErrorStatus(409, [
                'description' => 'Invalid deposit_type'
            ]);
            return $this->error(...['code' => 409, 'report_id' => 'MDR2']);
        }

        return $this->insert();
    }

    /** 
     * Function to insert data 
     * @return object
     */
    public function insert()
    {
        $dbRepo = new DBRepo($this->payload, $this->file, $this->auth);

        if ($dbRepo->requestPayment($depositId)) {

            ### If update success
            return $this->respond([
                'deposit_id' => base64_encode($depositId)
            ]);
        }

        ### If update fail
        return $this->error(500);
    }
}
