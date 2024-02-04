<?php

namespace App\REST\V1\Member\Delete;

use MVCME\REST\BaseDBRepo;
use MVCME\Database\Exceptions\DatabaseException;
use MVCME\Database\Exceptions\DataException;
use App\Models\Account;
use App\Models\Member\Member;
use App\Models\Member\MemberView;
use App\Models\Member\Request;

/**
 * 
 */
class DBRepo extends BaseDBRepo
{
    private $dbAccount;
    private $dbMember;
    private $dbMemberView;
    private $dbMemberRequest;

    public function __construct(?array $payload = [], ?array $file = [], ?array $auth = [])
    {
        parent::__construct($payload, $file, $auth);

        $this->dbAccount = new Account();
        $this->dbMember = new Member();
        $this->dbMemberView = new MemberView();
        $this->dbMemberRequest = new Request();
    }


    /*
     * ---------------------------------------------
     * TOOLS
     * ---------------------------------------------
     */

    /**
     * Function to check whether account active or not
     * @return array|object
     */
    public function checkMemberDeleted($accUUID)
    {
        $data = $this->dbMemberView
            ->selectColumn(['member_id'])
            ->all([
                'uuid' => $accUUID,
                'deleted' => false,
            ]);

        return $data != null;
    }

    /**
     * Check whether there are requests from members that have not been completed
     * @return array|object
     */
    public function checkMemberRequest($accUUID)
    {
        $data = $this->dbMemberRequest
            ->selectColumn(['member_id'])
            ->all([
                'uuid' => $accUUID,
                'actived' => true,
            ]);

        return $data == null;
    }

    /*
     * ---------------------------------------------
     * DATABASE TRANSACTION
     * ---------------------------------------------
     */

    /**
     * Function to delete data from database
     * @return bool
     */
    public function requestDelete()
    {
        $member = $this->dbMember
            ->selectColumn(['member_id'])
            ->all([
                'uuid' => $this->auth['uuid'],
                'deleted' => false
            ]);

        $memberId = $member[0]['member_id'] ?? null;

        // Start database transaction
        $this->dbMember->db
            ->transException(true)
            ->transBegin();

        try {

            // If id found and Delete keys that have a null value
            $dbPayload = removeNullValues([
                'pm_metaUpdatedAt' => date('Y-m-d H:i:s'),
            ]);

            // Update table data
            $updateStatus = $this->dbMember->update(
                ['pm_id' => $memberId],
                $dbPayload
            );

            ## Insert member request table
            if ($updateStatus) {

                // If id found and Delete keys that have a null value
                $dbPayload = removeNullValues([
                    'pm_id' => $memberId,
                    'pmr_type' => 'DELETE',
                    'pmr_reason' => $this->payload['reason'],
                    'pmr_metaStatusActive' => true
                ]);

                // Insert table data
                $insertStatus = $this->dbMemberRequest->insert($dbPayload);

                if (!$insertStatus)
                    throw new DatabaseException("Failed when insert data into table \"{$this->dbMemberRequest->table}\"");
            }

            // Commit database transaction
            $this->dbMember->db->transCommit();

            // Return transaction status
            return $this->dbMember->db->transStatus();
        } catch (DatabaseException $e) {

            // Restore table data to a previous state if there are errors
            $this->dbMember->db->transRollback();

            // Print detail of database exception
            self::printDBException($e->getMessage());
            return false;
        }
    }

    /**
     * Function to cancel delete data from database
     * @return bool
     */
    public function cancelDelete()
    {
        $requestId = $this->dbMemberRequest
            ->selectColumn(['request_id'])
            ->all([
                'uuid' => $this->auth['uuid'],
                'actived' => true
            ]);

        $requestId = $requestId[0]['request_id'] ?? null;

        // Start database transaction
        $this->dbMemberRequest->db
            ->transException(true)
            ->transBegin();

        try {

            // Update table data
            $this->dbMemberRequest->delete(['pmr_id' => $requestId]);

            // Commit database transaction
            $this->dbMemberRequest->db->transCommit();

            // Return transaction status
            return $this->dbMemberRequest->db->transStatus();
        } catch (DatabaseException $e) {

            // Restore table data to a previous state if there are errors
            $this->dbMemberRequest->db->transRollback();

            // Print detail of database exception
            self::printDBException($e->getMessage());
            return false;
        }
    }
}
