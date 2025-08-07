<?php

namespace App\REST\V1\Manage\Member\Register;

use App\Models\Account;
use MVCME\REST\BaseDBRepo;
use MVCME\Database\Exceptions\DatabaseException;
use MVCME\Database\Exceptions\DataException;
use App\Models\Member\Member;
use App\Models\Member\Identity as MemberIdentity;
use App\Models\Member\Business as MemberBusiness;
use App\Models\Member\MemberView;

/**
 * 
 */
class DBRepoManual extends BaseDBRepo
{
    private $dbAccount;
    private $dbMember;
    private $dbMemberIdentity;
    private $dbMemberBusiness;
    private $dbMemberView;

    public function __construct(?array $payload = [], ?array $file = [], ?array $auth = [])
    {
        parent::__construct($payload, $file, $auth);

        $this->dbAccount = new Account();
        $this->dbMember = new Member();
        $this->dbMemberIdentity = new MemberIdentity();
        $this->dbMemberBusiness = new MemberBusiness();
        $this->dbMemberView = new MemberView();
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
        // $data = $this->dbMember
        //     ->selectColumn(['uuid'])
        //     ->all([
        //         'member_id' => $memberId,
        //         'deleted' => false,
        //         'state_code' => 'WT_VALIDATION'
        //     ]);

        // return $data != null;
    }


    /*
     * ---------------------------------------------
     * DATABASE TRANSACTION
     * ---------------------------------------------
     */


    /**
     * Function to insert data into database
     * @return bool
     */
    public function manualInput()
    {
        ## Formatting additional data which not payload
        // Try to get pa_id from uuid
        $account = $this->dbAccount
            ->selectColumn(['account_id'])
            ->all([
                'uuid' => 'acff74f5-b320-4113-9f35-221e85e5caeb'
            ]);
        $paId = $account[0]['account_id'];

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
                'pa_id' => $paId,
                'pm_registerNumber' => $this->payload['register_number'] ?? null,
                'pm_nickname' => $this->payload['nickname'] ?? null,
                'pm_addressDomicile' => $this->payload['address_domicile'] ?? null,
                'pm_phoneNumber' => $this->payload['phone_number'] ?? null,
                'pm_waNumber' => $this->payload['wa_number'] ?? null,
                'pm_metaStatusActive' => true,
                'pm_verifiedBy' => $this->auth['account_id'],
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
                'pmi_nik' => $this->payload['nik'] ?? null,
                'pmi_fullname' => $this->payload['fullname'] ?? null,
                'pmi_birthPlace' => $this->payload['birth_place'] ?? null,
                'pmi_birthDate' => $this->payload['birth_date'] ?? null,
                'pmi_gender' => $this->payload['gender'] ?? null,
                'pmi_address' => $this->payload['address'] ?? null,
                'pmi_npwp' => $this->payload['npwp'] ?? null,
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
                'pmb_registrationNumber' => $this->payload['registration_number'] ?? null,
                'pmb_registrationType' => $this->payload['registration_type'] ?? null,
                'pmb_businessName' => $this->payload['business_name'] ?? null,
                'pmb_businessAddress' => $this->payload['business_address'] ?? null,
                'pmb_businessNPWP' => $this->payload['business_npwp'] ?? null,
                'pmb_businessPhoneNumber' => $this->payload['business_phone_number'] ?? null,
                'pmb_businessEmail' => $this->payload['business_email'] ?? null,
                'pmb_businessRegistrationDate' => $this->payload['business_registration_date'] ?? null,
                'pmb_businessLegal' => isset($this->payload['business_legal']) ? json_encode($this->payload['business_legal']) : null
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
}
