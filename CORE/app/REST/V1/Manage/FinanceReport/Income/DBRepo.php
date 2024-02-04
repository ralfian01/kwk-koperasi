<?php

namespace App\REST\V1\Manage\FinanceReport\Income;

use MVCME\REST\BaseDBRepo;
use MVCME\Database\Exceptions\DatabaseException;
use MVCME\Database\Exceptions\DataException;
use App\Models\FinanceReport\FinanceReport;

/**
 * 
 */
class DBRepo extends BaseDBRepo
{
    private $dbFinanceReport;

    public function __construct(?array $payload = [], ?array $file = [], ?array $auth = [])
    {
        parent::__construct($payload, $file, $auth);

        $this->dbFinanceReport = new FinanceReport();
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
    public function checkFinanceReportDeletable($financeReportId)
    {
        $data = $this->dbFinanceReport
            ->selectColumn(['finance_report_id'])
            ->all([
                'finance_report_id' => $financeReportId,
                'finance_report_type' => 'IN',
                'deletable' => true
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
        ## Formatting additional data which not payload
        // Formatting date range
        if (isset($this->payload['date_range'])) {
            if (!is_array($this->payload['date_range'])) {
                $this->payload['date_range'] = explode(',', $this->payload['date_range']);
            }

            if (count($this->payload['date_range']) < 2) {
                $this->payload['date_range'] = [$this->payload['date_range'], date('Y-m-d')];
            }

            $this->payload['date_range'][0] .= ' 00:00:00';
            $this->payload['date_range'][1] .= ' 23:59:59';
        }

        try {

            ### Get client data
            $filterPayload = removeNullValues([
                'finance_report_type' => 'IN',
                'finance_report_id' => isset($this->payload['id']) ? base64_decode($this->payload['id']) : null,
                'finance_report_description' => $this->payload['description'] ?? null,
                'finance_report_amount' => $this->payload['amount'] ?? null,
                'finance_report_date' => $this->payload['date'] ?? null,
            ]);

            // Create another way to get data
            if (isset($this->payload['date_range'])) {

                // Get data custom way
                $data = $this->dbFinanceReport
                    ->where("(pts_finance_report.pfr_createdAt BETWEEN '{$this->payload['date_range'][0]}' AND '{$this->payload['date_range'][1]}')")
                    ->all(
                        $filterPayload,
                        isset($this->payload['limit']) ? explode(',', $this->payload['limit']) : null
                    );

                return $data ?? null;
            }

            // Get all data
            $data = $this->dbFinanceReport
                ->orderBy('pts_finance_report.pfr_createdAt', 'DESC')
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
     * Function to insert data into database
     * @return bool
     */
    public function insertIncome()
    {
        ## Formatting additional data which not payload
        // Code here ...

        ## Formatting payload
        // Code here ...

        // Start database transaction
        $this->dbFinanceReport->db
            ->transException(true)
            ->transBegin();

        try {

            // If id found and Delete keys that have a null value
            $dbPayload = removeNullValues([
                'pfr_reportType' => 'IN',
                'pfr_description' => $this->payload['description'],
                'pfr_amount' => $this->payload['amount'],
                'pfr_createdBy' => $this->auth['account_id']
            ]);

            // Insert table data
            $this->dbFinanceReport->insert($dbPayload);

            // Commit database transaction
            $this->dbFinanceReport->db->transCommit();

            // Return transaction status
            return $this->dbFinanceReport->db->transStatus();
        } catch (DatabaseException $e) {

            // Restore table data to a previous state if there are errors
            $this->dbFinanceReport->db->transRollback();

            // Print detail of database exception
            self::printDBException($e->getMessage());
            return false;
        }
    }

    /**
     * Function to delete data from database
     * @return bool
     */
    public function deleteIncome()
    {
        ## Formatting additional data which not payload
        // Code here ...

        ## Formatting payload
        // Code here ...

        // Start database transaction
        $this->dbFinanceReport->db
            ->transException(true)
            ->transBegin();

        try {

            // Delete table data
            $this->dbFinanceReport->delete(
                ['id' => base64_decode($this->payload['id'])]
            );

            // Commit database transaction
            $this->dbFinanceReport->db->transCommit();

            // Return transaction status
            return $this->dbFinanceReport->db->transStatus();
        } catch (DatabaseException $e) {

            // Restore table data to a previous state if there are errors
            $this->dbFinanceReport->db->transRollback();

            // Print detail of database exception
            self::printDBException($e->getMessage());
            return false;
        }
    }
}
