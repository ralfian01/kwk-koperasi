<?php

namespace App\REST\V1\Manage\Member;

use MVCME\REST\BaseDBRepo;
use MVCME\Database\Exceptions\DatabaseException;
use MVCME\Database\Exceptions\DataException;
use App\Models\Member\MemberView;

/**
 * 
 */
class DBRepo extends BaseDBRepo
{
    private $dbMemberView;

    public function __construct(?array $payload = [], ?array $file = [], ?array $auth = [])
    {
        parent::__construct($payload, $file, $auth);

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
     * Function to get data from database
     * @return bool
     */
    public function getData()
    {
        try {

            ### Get client data
            $filterPayload = removeNullValues([
                'member_id' => isset($this->payload['id']) ? base64_decode($this->payload['id']) : null,
                'actived' => $this->payload['actived'] ?? null,
                'deleted' => false,
                'phone_number' => $this->payload['phone_number'] ?? null,
                'wa_number' => $this->payload['wa_number'] ?? null,
                'fullname' => $this->payload['fullname'] ?? null,
                'gender' => $this->payload['gender'] ?? null,
                'npwp' => $this->payload['npwp'] ?? null,
                'nik' => $this->payload['nik'] ?? null,
                'role_code' => ['MASTER_ADMIN', 'ADMIN', 'CHAIRMAN', 'MANAGER', 'MEMBER']
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
}
