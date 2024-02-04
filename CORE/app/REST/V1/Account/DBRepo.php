<?php

namespace App\REST\V1\Account;

use MVCME\REST\BaseDBRepo;
use MVCME\Database\Exceptions\DatabaseException;
use MVCME\Database\Exceptions\DataException;
use App\Models\Account;
use App\Models\Member\Member;
use App\Models\View\AccountView;

/**
 * 
 */
class DBRepo extends BaseDBRepo
{
    private $dbAccount;
    private $dbAccountView;
    private $dbMember;

    public function __construct(?array $payload = [], ?array $file = [], ?array $auth = [])
    {
        parent::__construct($payload, $file, $auth);

        $this->dbAccount = new Account();
        $this->dbAccountView = new AccountView();
        $this->dbMember = new Member();
    }


    /*
     * ---------------------------------------------
     * TOOLS
     * ---------------------------------------------
     */

    /**
     * Function to validate account password
     * @return bool
     */
    public function validateAccountPassword($accUUID, $password)
    {
        $data = $this->dbAccount
            ->selectColumn([
                'uuid'
            ])
            ->all([
                'uuid' => $accUUID,
                'password' => hash('SHA256', $password),
                'deleted' => false,
                'actived' => true,
                'deletable' => true
            ]);

        return $data != null;
    }


    /*
     * ---------------------------------------------
     * DATABASE TRANSACTION
     * ---------------------------------------------
     */

    /**
     * Function to get data from database
     * @return bool
     */
    public function getData()
    {
        try {

            ### Get client data

            // Get all data
            $data = $this->dbAccountView
                ->selectColumn([
                    'uuid', 'username', 'privilege', 'role'
                ])
                ->data([
                    'uuid' => $this->auth['uuid']
                ]);

            return $data;
        } catch (DatabaseException $e) {

            // Print detail of database exception
            self::printDBException($e->getMessage());
            return false;
        }
    }


    /**
     * Function to delete data from database
     * @return bool
     */
    public function deleteData()
    {
        ## Formatting additional data which not payload
        // Get member data
        $member = $this->dbMember
            ->selectColumn(['member_id'])
            ->all(['account_id' => $this->auth['account_id']]);
        $memberId = $member[0]['member_id'] ?? null;

        ## Formatting payload
        // Code here...

        // Start database transaction
        $this->dbAccount->db
            ->transException(true)
            ->transBegin();

        try {

            // If id found and Delete keys that have a null value
            $dbPayload = removeNullValues([
                'pa_metaStatusDelete' => 1,
                'pa_metaStatusActive' => 0,
                'pa_updatedAt' => date('Y-m-d H:i:s'),
            ]);

            // Update table data
            $updateStatus = $this->dbAccount->update(
                ['pa_id' => $this->auth['account_id']],
                $dbPayload
            );

            if (!$updateStatus)
                throw new DatabaseException("Failed when update data into table \"{$this->dbAccount->table}\"");

            ## Delete member data
            if ($memberId != null) {

                $dbPayload = removeNullValues([
                    'pm_metaStatusDelete' => 1,
                    'pm_updatedAt' => date('Y-m-d H:i:s'),
                ]);

                $this->dbMember->update([
                    ['pm_id' => $memberId],
                    $dbPayload
                ]);

                if (!$updateStatus)
                    throw new DatabaseException("Failed when update data into table \"{$this->dbMember->table}\"");
            }

            // Commit database transaction
            $this->dbAccount->db->transCommit();

            // Return transaction status
            return $this->dbAccount->db->transStatus();
        } catch (DatabaseException $e) {

            // Restore table data to a previous state if there are errors
            $this->dbAccount->db->transRollback();

            // Print detail of database exception
            self::printDBException($e->getMessage());
            return false;
        }
    }
}
