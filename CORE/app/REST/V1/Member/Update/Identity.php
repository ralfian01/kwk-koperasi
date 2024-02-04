<?php

namespace App\REST\V1\Member\Update;

use App\REST\V1\BaseRESTV1;

class Identity extends BaseRESTV1
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
        'nik' => [],
        'fullname' => ['maxlength[100]'],
        'birth_place' => [],
        'birth_date' => ['date_ymd'],
        'gender' => ['enum[M, F]'],
        'address' => ['maxlength[355]'],
        'npwp' => [],
        'photo_id_card' => ['single_file', 'file_accept[png, jpg, jpeg, pdf]'],
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
        // Make sure account is active
        if (!$this->dbRepo->checkAccountActive($this->auth['uuid'])) {
            return $this->error(401, [], 'MUI1');
        }

        // Make sure member is not deleted
        if (!$this->dbRepo->checkMemberDeleted($this->auth['uuid'])) {
            $this->setErrorStatus(
                403,
                ['description' => 'You have not yet registered to become a member']
            );
            return $this->error(403, [], 'MUI2');
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

        if ($dbRepo->updateMemberIdentity()) {

            ### If update success
            return $this->respond([]);
        }

        ### If update fail
        return $this->error(500);
    }
}
