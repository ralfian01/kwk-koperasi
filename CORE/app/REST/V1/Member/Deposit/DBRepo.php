<?php

namespace App\REST\V1\Member\Deposit;

use MVCME\REST\BaseDBRepo;
use MVCME\Database\Exceptions\DatabaseException;
use MVCME\Database\Exceptions\DataException;
use App\Models\Deposit;
use App\Models\Member\Deposit\MemberDeposit;
use App\Models\Member\Deposit\MemberDepositPayment;
use App\Models\Member\Member;

/**
 * 
 */
class DBRepo extends BaseDBRepo
{
    private $dbMember;
    private $dbDeposit;
    private $dbMemberDeposit;
    private $dbMemberDepositPayment;

    public function __construct(?array $payload = [], ?array $file = [], ?array $auth = [])
    {
        parent::__construct($payload, $file, $auth);

        $this->dbMember = new Member();
        $this->dbDeposit = new Deposit();
        $this->dbMemberDeposit = new MemberDeposit();
        $this->dbMemberDepositPayment = new MemberDepositPayment();
    }


    /*
     * ---------------------------------------------
     * TOOLS
     * ---------------------------------------------
     */

    /**
     * Function to check whether deposit code is valid or not
     * @return array|object
     */
    public function checkDepositCode($depositCode)
    {
        $data = $this->dbDeposit
            ->selectColumn(['deposit_id'])
            ->all([
                'deposit_code' => $depositCode
            ]);

        return $data != null;
    }

    /**
     * Function to check whether member active or not
     * @return array|object
     */
    public function checkMemberActive($accUUID)
    {
        $data = $this->dbMember
            ->selectColumn(['member_id'])
            ->all([
                'uuid' => $accUUID,
                'deleted' => false,
                'actived' => true
            ]);

        return $data != null;
    }

    /**
     * Function to check whether member active or not
     * @return array|object
     */
    public function checkActiveDeposit($depositId)
    {
        $data = $this->dbMemberDeposit
            ->selectColumn(['deposit_id'])
            ->all([
                'deposit_id' => $depositId,
                'actived' => true
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
        ## Formatting payload
        // Code here ...

        try {

            ### Get client data
            $filterPayload = removeNullValues([
                'deposit_id' => isset($this->payload['id']) ? base64_decode($this->payload['id']) : null,
                'account_id' => $this->auth['account_id'],
                'created_at' => $this->payload['created_at'] ?? null,
                'deposit_type' => $this->payload['deposit_type'] ?? null,
                'payment_status' => $this->payload['payment_status'] ?? null,
                'actived' => isset($this->payload['deposit_status']) ? $this->payload['deposit_status'] == 'ACTIVE' : null,
            ]);

            // Create another way to get data
            if (isset($this->payload['payment_status_in'])) {

                $wherePaymentStatus = "pts_member__deposit_payment.pmdp_paymentStatus IN ('" . implode("','", $this->payload['payment_status_in']) . "')";

                if (in_array('NOT_PAID', $this->payload['payment_status_in'])) {
                    $wherePaymentStatus .= " OR pts_member__deposit_payment.pmdp_paymentStatus IS NULL";
                }

                // Get data custom way
                $data = $this->dbMemberDeposit
                    ->excludeColumn([
                        'member_id', 'account_id', 'username'
                    ])
                    ->where("({$wherePaymentStatus})")
                    ->all(
                        $filterPayload,
                        isset($this->payload['limit']) ? explode(',', $this->payload['limit']) : null
                    );

                return $data ?? null;
            }

            // Get all data
            $data = $this->dbMemberDeposit
                ->excludeColumn([
                    'member_id', 'account_id', 'username'
                ])
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
    public function requestPayment(&$insertId)
    {
        ## Formatting additional data which not payload
        // Get member data
        $member = $this->dbMember
            ->selectColumn(['member_id'])
            ->all([
                'uuid' => $this->auth['uuid'],
                'deleted' => false
            ]);

        $memberId = $member[0]['member_id'] ?? null;

        ## Formatting payload
        // Get deposit id
        $deposit = $this->dbDeposit
            ->selectColumn(['deposit_id', 'deposit_amount'])
            ->all([
                'deposit_code' => $this->payload['deposit_type']
            ]);

        $deposit = $deposit[0] ?? null;

        // Start database transaction
        $this->dbMemberDeposit->db
            ->transException(true)
            ->transBegin();

        try {

            // If id found and Delete keys that have a null value
            $dbPayload = removeNullValues([
                'pm_id' => $memberId,
                'pd_id' => $deposit['deposit_id'],
                'pmd_amount' => $deposit['deposit_amount'] == 0 ? $this->payload['amount'] : $deposit['deposit_amount'],
            ]);

            // Insert table data
            $insertId = $this->dbMemberDeposit->insert($dbPayload);


            // Commit database transaction
            $this->dbMemberDeposit->db->transCommit();

            // Return transaction status
            return $this->dbMemberDeposit->db->transStatus();
        } catch (DatabaseException $e) {

            // Restore table data to a previous state if there are errors
            $this->dbMemberDeposit->db->transRollback();

            // Print detail of database exception
            self::printDBException($e->getMessage());
            return false;
        }
    }

    /**
     * Function to update data from database
     * @return bool
     */
    public function cancelRequestPayment()
    {
        ## Formatting additional data which not payload
        // Code here...

        ## Formatting payload
        // Code here...

        // Start database transaction
        $this->dbMemberDeposit->db
            ->transException(true)
            ->transBegin();

        try {

            // Delete table data
            $this->dbMemberDeposit->delete([
                'pmd_id' => base64_decode($this->payload['id'])
            ]);


            // Commit database transaction
            $this->dbMemberDeposit->db->transCommit();

            // Return transaction status
            return $this->dbMemberDeposit->db->transStatus();
        } catch (DatabaseException $e) {

            // Restore table data to a previous state if there are errors
            $this->dbMemberDeposit->db->transRollback();

            // Print detail of database exception
            self::printDBException($e->getMessage());
            return false;
        }
    }

    /**
     * Function to insert data from database
     * @return bool
     */
    public function payment()
    {
        ## Formatting additional data which not payload
        // Code here...

        ## Formatting payload
        // Code here...

        // Start database transaction
        $this->dbMemberDepositPayment->db
            ->transException(true)
            ->transBegin();

        try {

            // If id found and Delete keys that have a null value
            $dbPayload = removeNullValues([
                'pmd_id' => base64_decode($this->payload['id']),
                'pmdp_paymentMethod' => $this->payload['payment_method'],
                'pmdp_paymentProof' => $this->payload['evidence'] ?? null,
                'pmdp_paymentStatus' => 'PENDING',
            ]);

            // Insert to deposit payment table data
            $insertId = $this->dbMemberDepositPayment->insert($dbPayload);

            if (!$insertId)
                throw new DatabaseException("Failed when insert data into table \"{$this->dbMemberDepositPayment->table}\"");

            ## Update deposit table
            $dbPayload = removeNullValues([
                'pmd_metaStatusActive' => false,
                'pmd_updatedAt' => date('Y-m-d H:i:s'),
            ]);

            $updateStatus = $this->dbMemberDeposit->update(
                ['pmd_id' => base64_decode($this->payload['id'])],
                $dbPayload
            );

            if (!$updateStatus)
                throw new DatabaseException("Failed when update data into table \"{$this->dbMemberDeposit->table}\"");


            // Commit database transaction
            $this->dbMemberDepositPayment->db->transCommit();

            // Return transaction status
            return $this->dbMemberDepositPayment->db->transStatus();
        } catch (DatabaseException $e) {

            // Restore table data to a previous state if there are errors
            $this->dbMemberDepositPayment->db->transRollback();

            // Print detail of database exception
            self::printDBException($e->getMessage());
            return false;
        }
    }
}
