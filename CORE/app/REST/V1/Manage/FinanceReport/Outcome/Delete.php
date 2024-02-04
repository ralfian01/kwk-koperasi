<?php

namespace App\REST\V1\Manage\FinanceReport\Outcome;

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
        'id' => ['required', 'base64'],
    ];

    /* Edit this line to set privilege rules */
    protected $privilegeRules = [
        'FINANCE_REPORT_MANAGE_VIEW',
        'FINANCE_REPORT_MANAGE_DELETE',
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
        // Make sure finance report is deletable
        if (!$this->dbRepo->checkFinanceReportDeletable(base64_decode($this->payload['id']))) {
            $this->setErrorStatus(403, [
                'description' => 'You cannot delete this finance report'
            ]);
            return $this->error(...['code' => 403, 'report_id' => 'MFOD1']);
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

        if ($dbRepo->deleteOutcome()) {

            ### If update success
            return $this->respond([]);
        }

        ### If update fail
        return $this->error(500);
    }
}
