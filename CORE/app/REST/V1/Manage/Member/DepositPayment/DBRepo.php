<?php

namespace App\REST\V1\Manage\Member\DepositPayment;

use MVCME\REST\BaseDBRepo;
use MVCME\Database\Exceptions\DatabaseException;
use MVCME\Database\Exceptions\DataException;
use App\Models\Member\Deposit\MemberDepositPayment;

/**
 * 
 */
class DBRepo extends BaseDBRepo
{
    private $dbMemberDepositPayment;

    public function __construct(?array $payload = [], ?array $file = [], ?array $auth = [])
    {
        parent::__construct($payload, $file, $auth);

        $this->dbMemberDepositPayment = new MemberDepositPayment();
    }


    /*
     * ---------------------------------------------
     * TOOLS
     * ---------------------------------------------
     */

    /**
     * Function to check whether deposit payment has valid status or not
     * @return array|object
     */
    public function checkDepositPaymentStatus($depositId)
    {
        $data = $this->dbMemberDepositPayment
            ->selectColumn(['payment_id'])
            ->all([
                'deposit_id' => $depositId,
                'payment_status' => 'PENDING'
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
                'deposit_id' => isset($this->payload['id']) ? base64_decode($this->payload['id']) : null,
                'member_id' => isset($this->payload['member_id']) ? base64_decode($this->payload['member_id']) : null,
                'verified_by' => isset($this->payload['verifier_id']) ? base64_decode($this->payload['verifier_id']) : null,
                'created_at' => $this->payload['created_at'] ?? null,
                'deposit_type' => $this->payload['deposit_type'] ?? null,
                'payment_status' => $this->payload['payment_status'] ?? null,
                'payment_method' => $this->payload['payment_method'] ?? null,
            ]);

            // Get all data
            $data = $this->dbMemberDepositPayment
                ->orderBy('pts_member__deposit.pmd_createdAt', 'DESC')
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
    public function confirmPayment()
    {
        ## Formatting additional data which not payload
        $depositPayment = $this->dbMemberDepositPayment
            ->selectColumn(['payment_id'])
            ->all([
                'deposit_id' => base64_decode($this->payload['id'])
            ]);
        $depositPaymentId = $depositPayment[0]['payment_id'] ?? null;

        ## Formatting payload
        // Payment status if accepted by manager
        $pmdpPaymentStatus = 'VALID';

        if ($this->payload['accept'] == 'N') {
            // Payment status if rejected by manager
            $pmdpPaymentStatus = 'INVALID';
        }

        // Start database transaction
        $this->dbMemberDepositPayment->db
            ->transException(true)
            ->transBegin();

        try {

            // If id found and Delete keys that have a null value
            $dbPayload = removeNullValues([
                'pmdp_paymentStatus' => $pmdpPaymentStatus,
                'pmdp_verifiedBy' => $this->auth['account_id']
            ]);

            // Update table data
            $this->dbMemberDepositPayment->update(
                ['pmdp_id' => $depositPaymentId],
                $dbPayload
            );

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
