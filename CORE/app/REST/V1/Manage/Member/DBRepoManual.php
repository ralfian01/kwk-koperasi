<?php

namespace App\REST\V1\Manage\Member;

use MVCME\REST\BaseDBRepo;
use MVCME\Database\Exceptions\DatabaseException;
use MVCME\Database\Exceptions\DataException;
use App\Models\Member\Member;
use App\Models\Member\Identity as MemberIdentity;
use App\Models\Member\Business as MemberBusiness;
use App\Models\Member\MemberView;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

/**
 * 
 */
class DBRepoManual extends BaseDBRepo
{
    private $dbMember;
    private $dbMemberIdentity;
    private $dbMemberBusiness;
    private $dbMemberView;

    public function __construct(?array $payload = [], ?array $file = [], ?array $auth = [])
    {
        parent::__construct($payload, $file, $auth);

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



    /*
     * ---------------------------------------------
     * DATABASE TRANSACTION
     * ---------------------------------------------
     */

    /**
     * Function to insert data into database
     * @return bool
     */
    public function manualUpdate()
    {
        ## Formatting additional data which not payload
        // Try to get member identity id
        $memberIdentity = $this->dbMemberIdentity
            ->selectColumn(['pmi_id'])
            ->all([
                'member_id' => base64_decode($this->payload['id'])
            ]);
        $memberIdentityId = $memberIdentity[0]['pmi_id'] ?? null;

        // Try to get member business id
        $memberBusiness = $this->dbMemberBusiness
            ->selectColumn(['pmb_id'])
            ->all([
                'member_id' => base64_decode($this->payload['id'])
            ]);
        $memberBusinessId = $memberBusiness[0]['pmb_id'] ?? null;

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
                'pm_registerNumber' => $this->payload['register_number'] ?? null,
                'pm_nickname' => $this->payload['nickname'] ?? null,
                'pm_addressDomicile' => $this->payload['address_domicile'] ?? null,
                'pm_phoneNumber' => $this->payload['phone_number'] ?? null,
                'pm_waNumber' => $this->payload['wa_number'] ?? null,
                'pm_verifiedBy' => $this->auth['account_id'],
                'pm_metaStatusActive' => true,
                'pm_metaUpdatedAt' => date('Y-m-d H:i:s'),
                'pm_metaState' => json_encode([
                    'code' => 'MANUAL_OVERRIDE',
                    'value' => '1d7d4919-5424-4450-8c40-6ce4a5b661dd' // Fixed value
                ])
            ]);

            // Update data and return update Id
            $updateStatus = $this->dbMember->update(
                ['id' => base64_decode($this->payload['id'])],
                $dbPayload
            );

            if (!$updateStatus)
                throw new DatabaseException("Failed when update data into table \"{$this->dbMember->table}\"");

            ## Update member Identity dan Business data
            ### Update member identity data
            // If id found and Delete keys that have a null value
            $dbPayload = removeNullValues([
                'pmi_nik' => $this->payload['nik'] ?? null,
                'pmi_fullname' => $this->payload['fullname'] ?? null,
                'pmi_birthPlace' => $this->payload['birth_place'] ?? null,
                'pmi_birthDate' => $this->payload['birth_date'] ?? null,
                'pmi_gender' => $this->payload['gender'] ?? null,
                'pmi_address' => $this->payload['address'] ?? null,
                'pmi_npwp' => $this->payload['npwp'] ?? null,
            ]);

            // Update data and return update Id
            $updateStatus = $this->dbMemberIdentity->update(
                ['pmi_id' => $memberIdentityId],
                $dbPayload
            );

            // Throw report when update data failed
            if (!$updateStatus)
                throw new DatabaseException("Failed when update data into table \"{$this->dbMemberIdentity->table}\"");

            ### Update member business data
            // If id found and Delete keys that have a null value
            $dbPayload = removeNullValues([
                'pmb_registrationNumber' => $this->payload['registration_number'] ?? null,
                'pmb_registrationType' => $this->payload['registration_type'] ?? null,
                'pmb_businessName' => $this->payload['business_name'] ?? null,
                'pmb_businessAddress' => $this->payload['business_address'] ?? null,
                'pmb_businessNPWP' => $this->payload['business_npwp'] ?? null,
                'pmb_businessPhoneNumber' => $this->payload['business_phone_number'] ?? null,
                'pmb_businessEmail' => $this->payload['business_email'] ?? null,
                'pmb_businessRegistrationDate' => $this->payload['business_registration_date'] ?? null,
            ]);

            // Update data and return update Id
            $updateStatus = $this->dbMemberBusiness->update(
                ['pmb_id' => $memberBusinessId],
                $dbPayload
            );

            // Throw report when update data failed
            if (!$updateStatus)
                throw new DatabaseException("Failed when update data into table \"{$this->dbMemberBusiness->table}\"");


            ### Insert 


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
