<?php

namespace App\REST\V1\Manage\FinanceReport\Income;

use App\REST\V1\BaseRESTV1;

class Insert extends BaseRESTV1
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
        'description' => ['required', 'maxlength[500]'],
        'amount' => ['required'],
    ];

    /* Edit this line to set privilege rules */
    protected $privilegeRules = [
        'FINANCE_REPORT_MANAGE_VIEW',
        'FINANCE_REPORT_MANAGE_ADD',
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

        return $this->insert();
    }

    /** 
     * Function to insert data 
     * @return object
     */
    public function insert()
    {
        $dbRepo = new DBRepo($this->payload, $this->file, $this->auth);

        if ($dbRepo->insertIncome()) {

            ### If update success
            return $this->respond([]);
        }

        ### If update fail
        return $this->error(500);
    }
}
