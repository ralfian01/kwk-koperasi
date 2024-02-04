<?php

namespace App\REST\V1\Registration\Member;

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
        // Make sure member has valid state
        if (!$this->dbRepo->checkMemberPaymentState($this->auth['uuid'])) {
            $this->setErrorStatus(403, [
                'description' => 'You cannot done payment this way'
            ]);
            return $this->error(...['code' => 403, 'report_id' => 'RMP1']);
        }

        // Make sure member has active deposit request
        if (!$this->dbRepo->checkActiveDeposit($this->auth['uuid'])) {
            $this->setErrorStatus(403, [
                'description' => 'You do not have active deposit request'
            ]);
            return $this->error(...['code' => 403, 'report_id' => 'RMP2']);
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
