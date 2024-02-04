<?php

namespace App\REST\V1\Account\Security\Password;

use MVCME\REST\BaseDBRepo;
use MVCME\Database\Exceptions\DatabaseException;
use MVCME\Database\Exceptions\DataException;
use App\Models\Account;
use Firebase\JWT\JWT;
use AppConfig\TopSecret;
use Firebase\JWT\Key;

/**
 * 
 */
class DBRepo extends BaseDBRepo
{
    private $dbAccount;

    public function __construct(?array $payload = [], ?array $file = [], ?array $auth = [])
    {
        parent::__construct($payload, $file, $auth);

        $this->dbAccount = new Account();
    }


    /*
     * ---------------------------------------------
     * TOOLS
     * ---------------------------------------------
     */

    /**
     * Function to check account state token from meta state
     * @return array|object
     */
    public function validateAccountState($accUUID)
    {
        $data = $this->dbAccount
            ->selectColumn(['uuid'])
            ->all([
                'uuid' => $accUUID,
                'state_code' => 'CHANGE_PASSWORD'
            ]);

        return $data != null;
    }

    /**
     * Function to check account state token and password from meta state
     * @return array|object
     */
    public function validatePassToken($accUUID, $token, $newPassword)
    {
        $data = $this->dbAccount
            ->selectColumn(['pa_metaState'])
            ->all([
                'uuid' => $accUUID,
                'state_code' => 'CHANGE_PASSWORD'
            ]);

        if ($data == null)
            return false;

        $secret = new TopSecret();
        $data = $data[0];

        // Get account meta state token
        $jwtObject = JWT::decode($data['pa_metaState']['value'], new Key($secret->origin('bearer_key'), 'HS256'));

        // Check whether the token in the database with the request token is the same or not
        if ($jwtObject->token != hash('SHA256', $token)) return false;

        // Check whether the new password in the database with the request new password is the same or not
        if ($jwtObject->new_pass != hash('SHA256', $newPassword)) return false;
        return true;
    }

    /**
     * Function to validate account password
     * @return bool
     */
    public function validateAccountPassword($accUUID, $password)
    {
        $data = $this->dbAccount
            ->selectColumn(['uuid'])
            ->all([
                'uuid' => $accUUID,
                'password' => hash('SHA256', $password),
                'deleted' => false,
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
     * Function to request token to change password
     * @return bool
     */
    public function requestUpdateToken(&$returnToken)
    {
        // Formatting additional data which not payload
        // #

        // Start database transaction
        $this->dbAccount->db
            ->transException(true)
            ->transBegin();

        try {

            ### Update data

            // Get 6 digit token
            $returnToken = rand(100000, 999999);

            $secret = new TopSecret();
            $reqTime = time();
            $expTime = $reqTime + (60 * 30); // 1 Minute * 30: Expires in 30 Minutes
            $jsonJWT = [
                'iat' => $reqTime,
                'exp' => $expTime,
                'new_pass' => hash('SHA256', $this->payload['new_pass']),
                'token' => hash('SHA256', $returnToken),
            ];


            // If id found and Delete keys that have a null value
            $dbPayload = removeNullValues([
                'pa_metaState' => json_encode([
                    'code' => 'CHANGE_PASSWORD',
                    'value' => JWT::encode($jsonJWT, $secret->origin('bearer_key'), 'HS256'),
                ])
            ]);

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

    /**
     * Function to update account password 
     * @return bool
     */
    public function updatePassword()
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
                'pa_password' => hash('SHA256', $this->payload['new_pass']),
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
