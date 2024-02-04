<?php

namespace App\REST\V1\Manage\Member\DepositPayment;

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
        'id' => ['base64'],
        'member_id' => ['base64'],
        'verifier_id' => ['base64'],
        'created_at' => ['date_ymd'],
        'deposit_type' => ['enum[BASE, MANDATORY, VOLUNTARY]'],
        'deposit_type_in' => [],
        'payment_status' => ['enum[PENDING, VALID, INVALID]'],
        'payment_status_in' => [],
        'payment_method' => ['enum[CASH, TRANSFER]'],
        'payment_method_in' => [],
    ];

    /* Edit this line to set privilege rules */
    protected $privilegeRules = [
        'DEPOSIT_MANAGE_VIEW',
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
