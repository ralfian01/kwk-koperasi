<?php

namespace App\REST\V1\Registration\Account;

use MVCME\REST\BaseDBRepo;
use MVCME\Database\Exceptions\DatabaseException;
use MVCME\Database\Exceptions\DataException;
use App\Models\Account;
use App\Models\View\RoleView;
use Ramsey\Uuid\Uuid;

/**
 * 
 */
class DBRepo extends BaseDBRepo
{
    private $dbAccount;
    private $dbRoleView;

    public function __construct(?array $payload = [], ?array $file = [], ?array $auth = [])
    {
        parent::__construct($payload, $file, $auth);

        $this->dbAccount = new Account();
        $this->dbRoleView = new RoleView();
    }


    /*
     * ---------------------------------------------
     * TOOLS
     * ---------------------------------------------
     */

    /**
     * Function to check client username usage
     * @return bool
     */
    public function checkUsernameUsage($username)
    {
        $data = $this->dbAccount
            ->selectColumn(['uuid'])
            ->all([
                'username' => $username,
                'deleted' => false
            ]);


        return $data == null;

        // // If Account UUID same as found UUID
        // $found = 0;
        // foreach ($data as $val) {
        //     if ($val['uuid'] == $accUUID) $found++;
        // }
        // return $found >= 1;
    }

    /**
     * Function to check whether account has valid state or not
     * @return array|object
     */
    public function validateAccountState($utmCode, &$return)
    {
        $data = $this->dbAccount
            ->selectColumn(['account_id', 'uuid', 'username'])
            ->all([
                'state_code' => "CONFIRM_EMAIL",
                'state_value' => $utmCode
            ]);

        $return = $data[0] ?? null;

        return $data != null;
    }


    /*
     * ---------------------------------------------
     * DATABASE TRANSACTION
     * ---------------------------------------------
     */

    /**
     * Function to insert new data to database
     * @return bool
     */
    public function insertData()
    {
        ## Formatting additional data which not payload
        // Try to get role id
        $role = $this->dbRoleView
            ->selectColumn(['role_id'])
            ->all(['role_code' => 'UNKNOWN']);
        $roleId = $role[0]['role_id'] ?? null;

        ## Formatting payload
        // Code here...

        // Start database transaction
        $this->dbAccount->db
            ->transException(true)
            ->transBegin();

        try {

            ### Insert client data

            // If id found and Delete keys that have a null value
            $dbPayload = [
                'pa_uuid' => Uuid::uuid4(),
                'pa_username' => $this->payload['email'],
                'pa_password' => hash('SHA256', $this->payload['password']),
                'pr_id' => $roleId, // Registered as Unkown user
                'pa_metaDeletable' => true,
                'pa_metaStatusDelete' => false,
                'pa_metaStatusActive' => true,
                // Temporary
                // 'pa_metaStatusActive' => false,
                // 'pa_metaState' => json_encode([
                //     'code' => 'CONFIRM_EMAIL',
                //     'value' => hash('SHA1', $this->payload['email'] . ":" . time()),
                // ]),
            ];

            // Insert data and return insert Id
            $this->dbAccount->insert($dbPayload);

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

    /**
     * Function to update account email in database
     * @return bool
     */
    public function confirmRegistration()
    {
        // Formatting additional data which not payload
        // #

        // Start database transaction
        $this->dbAccount->db
            ->transException(true)
            ->transBegin();

        try {

            ### Update data

            // If id found and Delete keys that have a null value
            $dbPayload = [
                'pa_metaState' => null,
                'pa_metaStatusActive' => true
            ];

            // Update data
            $this->dbAccount->update(
                ['pa_id' => $this->auth['account_id']],
                $dbPayload
            );

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
        } catch (DataException $e) {

            if (strpos('There is no data to update.', $e->getMessage()) >= 0) return true;
        }
    }
}
