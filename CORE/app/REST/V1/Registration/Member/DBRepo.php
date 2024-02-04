<?php

namespace App\REST\V1\Registration\Member;

use MVCME\REST\BaseDBRepo;
use MVCME\Database\Exceptions\DatabaseException;
use MVCME\Database\Exceptions\DataException;
use App\Models\Member\Business;
use App\Models\Member\Deposit\MemberDeposit;
use App\Models\Member\Identity;
use App\Models\Member\Member;
use App\REST\V1\Member\Deposit\Pay;

/**
 * 
 */
class DBRepo extends BaseDBRepo
{
    private $dbMember;
    private $dbMemberBusiness;
    private $dbMemberIdentity;
    private $dbMemberDeposit;

    public function __construct(?array $payload = [], ?array $file = [], ?array $auth = [])
    {
        parent::__construct($payload, $file, $auth);

        $this->dbMember = new Member();
        $this->dbMemberIdentity = new Identity();
        $this->dbMemberBusiness = new Business();
        $this->dbMemberDeposit = new MemberDeposit();
    }


    /*
     * ---------------------------------------------
     * TOOLS
     * ---------------------------------------------
     */

    /**
     * Check whether member has valid state or not
     * @return array|object
     */
    public function checkMemberState($accUUID)
    {
        $data = $this->dbMember
            ->selectColumn(['member_id'])
            ->all([
                'uuid' => $accUUID,
                'state_code' => 'WT_VALIDATION'
            ]);

        return $data != null;
    }

    /**
     * Check whether member has valid state to done payment or not
     * @return array|object
     */
    public function checkMemberPaymentState($accUUID)
    {
        $data = $this->dbMember
            ->selectColumn(['member_id'])
            ->all([
                'uuid' => $accUUID,
                'state_code' => 'WT_PAYMENT'
            ]);

        return $data != null;
    }

    /**
     * Limit each account to only having 1 active member data
     * @return array|object
     */
    public function checkMemberRegistered($accUUID)
    {
        $data = $this->dbMember
            ->selectColumn(['member_id'])
            ->all([
                'uuid' => $accUUID,
                'deleted' => false,
            ]);

        return $data == null;
    }

    /**
     * Function to check whether member active or not
     * @return array|object
     */
    public function checkActiveDeposit($accUUID)
    {
        $data = $this->dbMemberDeposit
            ->selectColumn(['deposit_id'])
            ->all([
                'uuid' => $accUUID,
                'actived' => true,
                'deposit_type' => 'BASE'
            ]);

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
        // Code here...

        ## Formatting payload
        // Code here...

        // Start database transaction
        $this->dbMember->db
            ->transException(true)
            ->transBegin();

        try {

            ### Insert client data

            // If id found and Delete keys that have a null value
            $dbPayload = removeNullValues([
                'pa_id' => $this->auth['account_id'],
                'pm_nickname' => $this->payload['nickname'],
                'pm_addressDomicile' => $this->payload['address_domicile'],
                'pm_phoneNumber' => $this->payload['phone_number'],
                'pm_waNumber' => $this->payload['wa_number'],
                'pm_metaState' => json_encode([
                    'code' => 'WT_VALIDATION',
                    'value' => null
                ]),
            ]);

            // Insert data and return insert Id
            $insertId = $this->dbMember->insert($dbPayload);

            if (!$insertId)
                throw new DatabaseException("Failed when insert data into table \"{$this->dbMember->table}\"");

            ## Insert member Identity dan Business data
            ### Insert member identity data
            // If id found and Delete keys that have a null value
            $dbPayload = removeNullValues([
                'pm_id' => $insertId,
                'pmi_nik' => $this->payload['nik'],
                'pmi_fullname' => $this->payload['fullname'],
                'pmi_birthPlace' => $this->payload['birth_place'],
                'pmi_birthDate' => $this->payload['birth_date'],
                'pmi_gender' => $this->payload['gender'],
                'pmi_address' => $this->payload['address'],
                'pmi_npwp' => $this->payload['npwp'],
                'pmi_photoIdCard' => $this->payload['photo_id_card'] ?? null,
            ]);

            // Insert data and return insert Id
            $insertStatus = $this->dbMemberIdentity->insert($dbPayload);

            // Throw report when insert data failed
            if (!$insertStatus)
                throw new DatabaseException("Failed when insert data into table \"{$this->dbMemberIdentity->table}\"");

            ### Insert member business data
            // If id found and Delete keys that have a null value
            $dbPayload = removeNullValues([
                'pm_id' => $insertId,
                'pmb_registrationNumber' => $this->payload['registration_number'],
                'pmb_registrationType' => $this->payload['registration_type'],
                'pmb_businessName' => $this->payload['business_name'],
                'pmb_businessAddress' => $this->payload['business_address'],
                'pmb_businessNPWP' => $this->payload['business_npwp'],
                'pmb_businessPhoneNumber' => $this->payload['business_phone_number'],
                'pmb_businessEmail' => $this->payload['business_email'],
                'pmb_businessRegistrationDate' => $this->payload['business_registration_date'],
                'pmb_businessDocument' => $this->payload['business_document'] ?? null,
            ]);

            // Insert data and return insert Id
            $insertStatus = $this->dbMemberBusiness->insert($dbPayload);

            // Throw report when insert data failed
            if (!$insertStatus)
                throw new DatabaseException("Failed when insert data into table \"{$this->dbMemberBusiness->table}\"");


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
     * Function to cancel membership registration
     * @return bool
     */
    public function cancelRegistration()
    {
        ## Formatting additional data which not payload
        $member = $this->dbMember
            ->selectColumn(['member_id'])
            ->all([
                'uuid' => $this->auth['uuid'],
                'deleted' => false
            ]);
        $memberId = $member[0]['member_id'] ?? null;

        ## Formatting payload
        // Code here...

        // Start database transaction
        $this->dbMember->db
            ->transException(true)
            ->transBegin();

        try {

            ### Delete member data
            $this->dbMember->delete([
                'pm_id' => $memberId
            ]);


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
     * Function payment
     * @return bool
     */
    public function payment()
    {
        ## Formatting additional data which not payload
        // Try to get member data
        $member = $this->dbMember
            ->selectColumn(['member_id'])
            ->all([
                'uuid' => $this->auth['uuid'],
                'deleted' => false
            ]);

        $memberId = $member[0]['member_id'] ?? null;

        // Try to get member deposit data
        $memberDeposit = $this->dbMemberDeposit
            ->selectColumn(['deposit_id'])
            ->all([
                'member_id' => $memberId
            ]);

        $memberDepositId = $memberDeposit[0]['deposit_id'] ?? null;

        ## Formatting payload
        // Code here...

        // Start database transaction
        $this->dbMember->db
            ->transException(true)
            ->transBegin();

        try {

            ## Update member table data
            $dbPayload = removeNullValues([
                'pm_metaState' => json_encode([
                    'code' => 'WT_PAYMENT_VALIDATION',
                    'value' => null
                ])
            ]);

            // Update member data
            $this->dbMember->update(
                ['pm_id' => $memberId],
                $dbPayload
            );

            ## Pay member deposit
            $payDeposit = new Pay(...[
                'payload' => array_merge(
                    [
                        'id' => base64_encode($memberDepositId),
                    ],
                    $this->payload
                ),
                'auth' => $this->auth
            ]);

            $payDeposit->update();


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
