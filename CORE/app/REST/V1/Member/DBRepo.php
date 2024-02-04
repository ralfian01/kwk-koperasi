<?php

namespace App\REST\V1\Member;

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

            // Get all data
            $data = $this->dbMemberView
                ->excludeColumn(['account_id'])
                ->all([
                    'uuid' => $this->auth['uuid'],
                    'deleted' => false
                ]);

            return $data[0] ?? null;
        } catch (DatabaseException $e) {

            // Print detail of database exception
            self::printDBException($e->getMessage());
            return false;
        }
    }
}
