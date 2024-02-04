<?php

namespace App\REST\V1\Manage\Member\DepositPayment;

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
        'id' => ['required', 'base64'],
        'accept' => ['required', 'enum[Y, N]'],
    ];

    /* Edit this line to set privilege rules */
    protected $privilegeRules = [
        'DEPOSIT_MANAGE_VIEW',
        'DEPOSIT_MANAGE_APPROVE'
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
        // Make sure deposit payment has valid status
        if (!$this->dbRepo->checkDepositPaymentStatus(base64_decode($this->payload['id']))) {
            $this->setErrorStatus(403, [
                'description' => 'Deposit payment has invalid status'
            ]);
            return $this->error(...['code' => 403, 'report_id' => 'MMDPC1']);
        }

        return $this->confirm();
    }

    /** 
     * Function to update data 
     * @return object
     */
    public function confirm()
    {
        $dbRepo = new DBRepo($this->payload, $this->file, $this->auth);

        if ($dbRepo->confirmPayment()) {

            ### If update success
            return $this->respond([]);
        }

        ### If update fail
        return $this->error(500);
    }
}
