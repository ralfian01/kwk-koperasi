<?php

namespace App\REST\V1\Manage\Member\Register;

use MVCME\REST\BaseDBRepo;
use MVCME\Database\Exceptions\DatabaseException;
use MVCME\Database\Exceptions\DataException;
use App\REST\V1\Member\Deposit\Request as DepositRequest;
use App\Models\Account;
use App\Models\Member\Deposit\MemberDeposit;
use App\Models\Member\Member;
use App\Models\Member\MemberView;
use App\Models\View\RoleView;
use App\REST\V1\Manage\Member\DepositPayment\Confirm as ConfirmDeposit;

/**
 * 
 */
class DBRepo extends BaseDBRepo
{
    private $dbAccount;
    private $dbRoleView;
    private $dbMember;
    private $dbMemberView;
    private $dbMemberDeposit;

    public function __construct(?array $payload = [], ?array $file = [], ?array $auth = [])
    {
        parent::__construct($payload, $file, $auth);

        $this->dbAccount = new Account();
        $this->dbRoleView = new RoleView();
        $this->dbMember = new Member();
        $this->dbMemberView = new MemberView();
        $this->dbMemberDeposit = new MemberDeposit();
    }


    /*
     * ---------------------------------------------
     * TOOLS
     * ---------------------------------------------
     */

    /**
     * Function to check whether member has valid state or not
     * @return array|object
     */
    public function checkMemberState($memberId)
    {
        $data = $this->dbMember
            ->selectColumn(['uuid'])
            ->all([
                'member_id' => $memberId,
                'deleted' => false,
                'state_code' => 'WT_VALIDATION'
            ]);

        return $data != null;
    }

    /**
     * Function to check whether member has valid state or not
     * @return array|object
     */
    public function checkMemberPaymentState($memberId)
    {
        $data = $this->dbMember
            ->selectColumn(['uuid'])
            ->all([
                'member_id' => $memberId,
                'deleted' => false,
                'state_code' => 'WT_PAYMENT_VALIDATION'
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
            $filterPayload = removeNullValues([
                'state_code' => $this->payload['state_code'] ?? ['WT_VALIDATION', 'WT_PAYMENT_VALIDATION', 'REGISTER_REJECT', 'WT_PAYMENT'],
                // 'state_code_in' => $this->payload['state_code_in'] ?? ['WT_VALIDATION', 'WT_PAYMENT_VALIDATION', 'REGISTER_REJECT', 'WT_PAYMENT'],
                'deleted' => false,
                'phone_number' => $this->payload['phone_number'] ?? null,
                'wa_number' => $this->payload['wa_number'] ?? null,
                'fullname' => $this->payload['fullname'] ?? null,
                'gender' => $this->payload['gender'] ?? null,
                'npwp' => $this->payload['npwp'] ?? null,
                'nik' => $this->payload['nik'] ?? null,
                'id' => isset($this->payload['id']) ? base64_decode($this->payload['id']) : null,
            ]);

            // Get all data
            $data = $this->dbMemberView
                ->excludeColumn(['account_id'])
                ->all(
                    $filterPayload,
                    isset($this->payload['limit']) ? explode(',', $this->payload['limit']) : null
                );

            // Return single data
            if (isset($this->payload['id'])) {
                $data = $data[0] ?? null;
            }

            return $data ?? null;
        } catch (DatabaseException $e) {

            // Print detail of database exception
            self::printDBException($e->getMessage());
            return false;
        }
    }

    /**
     * Function to update data from database
     * @return bool
     */
    public function confirmMember()
    {
        ## Formatting additional data which not payload
        // Try to get role id
        $role = $this->dbRoleView
            ->selectColumn(['role_id'])
            ->all(['role_code' => 'NR_MEMBER']);
        $roleId = $role[0]['role_id'] ?? null;

        // Try to get account data from member id
        $account = $this->dbMember
            ->selectColumn(['account_id', 'uuid'])
            ->all([
                'member_id' => base64_decode($this->payload['id'])
            ]);
        $account = $account[0] ?? null;

        ## Formatting payload
        // Meta state if accepted by manager
        $pmMetaState = [
            'code' => 'WT_PAYMENT',
            'value' => null,
        ];

        if ($this->payload['accept'] == 'N') {
            // Meta state if accepted by manager
            $pmMetaState = [
                'code' => 'REGISTER_REJECT',
                'value' => null,
            ];
        }

        // Start database transaction
        $this->dbMember->db
            ->transException(true)
            ->transBegin();

        try {

            // If id found and Delete keys that have a null value
            $dbPayload = removeNullValues([
                'pm_metaUpdatedAt' => date('Y-m-d H:i:s'),
                'pm_metaState' => json_encode($pmMetaState),
                'pm_verifiedBy' => $this->auth['account_id'],
            ]);

            // Update table data
            $updateStatus = $this->dbMember->update(
                ['pm_id' => base64_decode($this->payload['id'])],
                $dbPayload
            );

            if (!$updateStatus)
                throw new DatabaseException("Failed when update data into table \"{$this->dbMember->table}\"");

            // When manager accept register
            if ($this->payload['accept'] == 'Y') {

                ## Update account table data
                $dbPayload = removeNullValues([
                    'pr_id' => $roleId // Set role to Not Registered Member
                ]);

                $updateStatus = $this->dbAccount->update(
                    ['pa_id' => $account['account_id']],
                    $dbPayload
                );

                if (!$updateStatus)
                    throw new DatabaseException("Failed when update data into table \"{$this->dbAccount->table}\"");

                ## Insert member deposit data
                $depositRequest = new DepositRequest(...[
                    'payload' => [
                        'deposit_type' => 'BASE',
                    ],
                    'auth' => [
                        'account_id' => $account['account_id'],
                        'uuid' => $account['uuid'],
                    ]
                ]);

                // Insert deposit request
                $depositRequest->insert();
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
     * Function to update data from database
     * @return bool
     */
    public function confirmPayment()
    {
        ## Formatting additional data which not payload
        // Try to get role id
        $role = $this->dbRoleView
            ->selectColumn(['role_id'])
            ->all(['role_code' => 'MEMBER']);
        $roleId = $role[0]['role_id'] ?? null;

        // Try to get account data from member id
        $account = $this->dbMember
            ->selectColumn(['account_id', 'uuid'])
            ->all([
                'member_id' => base64_decode($this->payload['id'])
            ]);
        $account = $account[0] ?? null;

        // Try to produce member register number
        $member = $this->dbMember
            ->select('pm_registerNumber')
            ->where('pm_registerNumber IS NOT NULL')
            ->orderBy('pm_registerNumber', 'DESC')
            ->limit(1, 0)
            ->find();

        $memberRegisterNumber = $member[0]['pm_registerNumber'] ?? null;
        $memberRegisterNumber += 1;

        // Try to get member deposit id from member id
        $memberDeposit = $this->dbMemberDeposit
            ->selectColumn(['deposit_id'])
            ->all([
                'member_id' => base64_decode($this->payload['id'])
            ]);
        $memberDepositId = $memberDeposit[0]['deposit_id'] ?? null;

        ## Formatting payload
        // Code here...

        // Start database transaction
        $this->dbMember->db
            ->transException(true)
            ->transBegin();

        try {

            // If id found and Delete keys that have a null value
            $dbPayload = [
                'pm_metaUpdatedAt' => date('Y-m-d H:i:s'),
                'pm_verifiedBy' => $this->auth['account_id'],
            ];

            if ($this->payload['accept'] == 'Y') {
                $dbPayload['pm_metaState'] = null;
                $dbPayload['pm_metaStatusActive'] = true;
                $dbPayload['pm_registerNumber'] = $memberRegisterNumber;
            }

            // Update table data
            $updateStatus = $this->dbMember->update(
                ['pm_id' => base64_decode($this->payload['id'])],
                $dbPayload
            );

            if (!$updateStatus)
                throw new DatabaseException("Failed when update data into table \"{$this->dbMember->table}\"");

            // When manager accept register
            if ($this->payload['accept'] == 'Y') {

                ## Update account table data
                $dbPayload = removeNullValues([
                    'pr_id' => $roleId // Set role to Not Registered Member
                ]);

                $updateStatus = $this->dbAccount->update(
                    ['pa_id' => $account['account_id']],
                    $dbPayload
                );

                if (!$updateStatus)
                    throw new DatabaseException("Failed when update data into table \"{$this->dbAccount->table}\"");

                ## Insert member deposit data
                $confirmDeposit = new ConfirmDeposit(...[
                    'payload' => [
                        'id' => base64_encode($memberDepositId),
                        'accept' => $this->payload['accept'],
                    ],
                    'auth' => $this->auth
                ]);

                // Update deposit request
                $confirmDeposit->confirm();
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
}
