<?php

namespace App\Middleware\Auth;

use MVCME\Middleware\MiddlewareInterface;
use MVCME\Request\HTTPRequestInterface;
use MVCME\Response\HTTPResponseInterface;
use App\Models\Account;
use App\Models\View\AccountView;
use AppConfig\TopSecret;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Exception;
use stdClass;

class CookieToken implements MiddlewareInterface
{
    private $secret;
    private $dbAcc;

    public function __construct()
    {
        $this->dbAcc = new AccountView();
        $this->secret = new TopSecret();
    }

    public function before(HTTPRequestInterface $request, $arguments = null)
    {
        $auth = [
            'status' => false,
            'data' => []
        ];

        if (isset($_COOKIE['_PTS-Auth:Token'])) {

            // Start check JWT Token age
            try {

                $jwtObject = JWT::decode($_COOKIE['_PTS-Auth:Token'], new Key($this->secret->origin('bearer_key'), 'HS256'));

                $accUUID = base64_decode($jwtObject->uid_b64);
                $accData = $this->dbAcc
                    ->selectColumn([
                        'account_id', 'uuid', 'username', 'pav_metaState', 'role', 'privilege'
                    ])
                    ->all([
                        'uuid' => $accUUID
                    ]);

                if ($accData != null && count($accData) >= 1)
                    $accData = $accData[0];

                if ($accData == null)
                    throw new Exception();

                $auth['status'] = true;
                $auth['data'] = $accData;
            } catch (Exception $exc) {

                $auth['status'] = false;
                $auth['data'] = [];
            }
        }

        // Return authentication
        $request->auth = new stdClass();
        $request->auth->status = $auth['status'];
        $request->auth->data = $auth['data'];

        return $request;
    }

    public function after(HTTPRequestInterface $request, HTTPResponseInterface $response, $arguments = null)
    {
        // Write code here
    }
}
