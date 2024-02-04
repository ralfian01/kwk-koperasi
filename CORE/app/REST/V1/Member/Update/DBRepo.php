<?php

namespace App\REST\V1\Member\Update;

use MVCME\REST\BaseDBRepo;
use MVCME\Database\Exceptions\DatabaseException;
use MVCME\Database\Exceptions\DataException;
use App\Models\Account;
use App\Models\Member\Business;
use App\Models\Member\Identity;
use App\Models\Member\Member;
use App\Models\Member\MemberView;

/**
 * 
 */
class DBRepo extends BaseDBRepo
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
        $this->dbMemberIdentity = new Identity();
        $this->dbMemberBusiness = new Business();
        $this->dbMemberView = new MemberView();
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
    public function checkAccountActive($accUUID)
    {
        $data = $this->dbAccount
            ->selectColumn(
                ['uuid']
            )
            ->all([
                'uuid' => $accUUID,
                'deleted' => false,
                'actived' => true,
            ]);

        if ($data == null) return false;
        return true;
    }

    /**
     * Function to check whether account active or not
     * @return array|object
     */
    public function checkMemberDeleted($accUUID)
    {
        $data = $this->dbMemberView
            ->selectColumn(
                ['member_id']
            )
            ->all([
                'uuid' => $accUUID,
                'deleted' => false,
            ]);

        if ($data == null) return false;
        return true;
    }

    /*
     * ---------------------------------------------
     * DATABASE TRANSACTION
     * ---------------------------------------------
     */

    /**
     * Function to update data from database
     * @return bool
     */
    public function updateMemberData()
    {
        $memberId = $this->dbMember
            ->selectColumn(['member_id'])
            ->all([
                'uuid' => $this->auth['uuid'],
                'deleted' => false
            ]);

        $memberId = $memberId[0]['member_id'] ?? null;

        // Start database transaction
        $this->dbMember->db
            ->transException(true)
            ->transBegin();

        try {

            // If id found and Delete keys that have a null value
            $dbPayload = removeNullValues([
                'pm_nickname' => $this->payload['nickname'] ?? null,
                'pm_addressDomicile' => $this->payload['address_domicile'] ?? null,
                'pm_phoneNumber' => $this->payload['phone_number'] ?? null,
                'pm_waNumber' => $this->payload['wa_number'] ?? null,
                'pm_metaUpdatedAt' => date('Y-m-d H:i:s')
            ]);

            // Update table data
            $this->dbMember->update(
                ['pm_id' => $memberId],
                $dbPayload
            );

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
    public function updateMemberIdentity()
    {
        $memberId = $this->dbMember
            ->selectColumn(['member_id'])
            ->all([
                'uuid' => $this->auth['uuid'],
                'deleted' => false
            ]);

        $memberId = $memberId[0]['member_id'] ?? null;

        // Start database transaction
        $this->dbMemberIdentity->db
            ->transException(true)
            ->transBegin();

        try {

            // If id found and Delete keys that have a null value
            $dbPayload = removeNullValues([
                'pmi_nik' => $this->payload['nik'] ?? null,
                'pmi_fullname' => $this->payload['fullname'] ?? null,
                'pmi_birthPlace' => $this->payload['birth_place'] ?? null,
                'pmi_birthDate' => $this->payload['birth_date'] ?? null,
                'pmi_gender' => $this->payload['gender'] ?? null,
                'pmi_address' => $this->payload['address'] ?? null,
                'pmi_npwp' => $this->payload['npwp'] ?? null,
                'pmi_photoIdCard' => $this->payload['photo_id_card'] ?? null,
            ]);

            // Update table data
            $this->dbMemberIdentity->update(
                ['pm_id' => $memberId],
                $dbPayload
            );

            // Commit database transaction
            $this->dbMemberIdentity->db->transCommit();

            // Return transaction status
            return $this->dbMemberIdentity->db->transStatus();
        } catch (DatabaseException $e) {

            // Restore table data to a previous state if there are errors
            $this->dbMember->db->transRollback();

            // Print detail of database exception
            self::printDBException($e->getMessage());
            return false;
        } catch (DataException $e) {

            if (strpos('There is no data to update.', $e->getMessage()) >= 0) return true;
        }
    }

    /**
     * Function to update data from database
     * @return bool
     */
    public function updateMemberBusiness()
    {
        $memberId = $this->dbMember
            ->selectColumn(['member_id'])
            ->all([
                'uuid' => $this->auth['uuid'],
                'deleted' => false
            ]);

        $memberId = $memberId[0]['member_id'] ?? null;

        // Start database transaction
        $this->dbMemberBusiness->db
            ->transException(true)
            ->transBegin();

        try {

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
                'pmb_businessDocument' => $this->payload['business_document'] ?? null,

            ]);

            // Update table data
            $this->dbMemberBusiness->update(
                ['pm_id' => $memberId],
                $dbPayload
            );

            // Commit database transaction
            $this->dbMemberBusiness->db->transCommit();

            // Return transaction status
            return $this->dbMemberBusiness->db->transStatus();
        } catch (DatabaseException $e) {

            // Restore table data to a previous state if there are errors
            $this->dbMemberBusiness->db->transRollback();

            // Print detail of database exception
            self::printDBException($e->getMessage());
            return false;
        }
    }
}
