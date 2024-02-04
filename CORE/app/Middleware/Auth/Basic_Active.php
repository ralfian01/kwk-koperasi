<?php

namespace App\Middleware\Auth;

use MVCME\Middleware\MiddlewareInterface;
use MVCME\Request\HTTPRequestInterface;
use MVCME\Response\HTTPResponseInterface;
use App\Models\View\AccountView;
use AppConfig\TopSecret;
use stdClass;

class Basic_Active implements MiddlewareInterface
{
    private $secret;
    private $dbAcc;

    public function __construct()
    {
        $this->secret = new TopSecret();
        $this->dbAcc = new AccountView();
    }

    public function before(HTTPRequestInterface $request, $arguments = null)
    {
        $user = $request->getServer('PHP_AUTH_USER');
        $pass = $request->getServer('PHP_AUTH_PW');
        $auth = [
            'status' => false,
            'data' => []
        ];

        if ($user != null && $pass != null) {

            // Basic Authentication with account username / email dan password
            $dbAcc = $this->dbAcc
                ->selectColumn([
                    'account_id', 'uuid', 'username', 'pav_metaState', 'role', 'privilege'
                ])
                ->all([
                    'username' => $user,
                    'password' => hash('SHA256', $pass),
                    'actived' => true,
                    'deleted' => false
                ]);

            if ($dbAcc != null) {

                $dbAcc = $dbAcc[0];
                $auth['status'] = true;
            } else if (
                $user == $this->secret->origin('basic_auth')['user']
                && $pass == $this->secret->origin('basic_auth')['pass']
            ) {

                // Basic Authentication with by pass username/email and password
                $auth['status'] = true;
            }

            if ($auth['status']) {

                $auth['data'] = [
                    'id' => $dbAcc['account_id'],
                    'uuid' => $dbAcc['uuid'],
                    'username' => $dbAcc['username']
                ];
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
