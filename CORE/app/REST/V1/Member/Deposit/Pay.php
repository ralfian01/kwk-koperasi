<?php

namespace App\REST\V1\Member\Deposit;

use App\REST\V1\BaseRESTV1;

class Pay extends BaseRESTV1
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
        'id' => ['required', 'base64'],
        'payment_method' => ['required', 'enum[CASH, TRANSFER]'],
        'evidence' => ['required', 'single_file', 'file_accept[jpg, jpeg, png, pdf]'],
    ];

    /* Edit this line to set privilege rules */
    protected $privilegeRules = [
        'MEMBER_DEPOSIT_VIEW',
        'MEMBER_DEPOSIT_PAY'
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
            return $this->error(...['code' => 403, 'report_id' => 'MDC1']);
        }

        // Make sure payload id is available
        if (!$this->dbRepo->checkActiveDeposit(base64_decode($this->payload['id']))) {
            $this->setErrorStatus(404, [
                'description' => 'ID could not be found'
            ]);
            return $this->error(...['code' => 404, 'report_id' => 'MDC2']);
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

        if ($dbRepo->payment()) {

            ### If update success
            return $this->respond([]);
        }

        ### If update fail
        return $this->error(500);
    }
}
