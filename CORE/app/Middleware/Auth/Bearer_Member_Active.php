<?php

namespace App\Middleware\Auth;

use MVCME\Middleware\MiddlewareInterface;
use MVCME\Request\HTTPRequestInterface;
use MVCME\Response\HTTPResponseInterface;
use App\Models\View\AccountView;
use App\Models\Member\Member;
use AppConfig\TopSecret;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Exception;
use stdClass;

/**
 * Authorization Bearer For Active Member
 */
class Bearer_Member_Active implements MiddlewareInterface
{
    private $secret;
    private $dbAcc;
    private $dbMember;

    public function __construct()
    {
        $this->secret = new TopSecret();
        $this->dbAcc = new AccountView();
        $this->dbMember = new Member();
    }

    public function before(HTTPRequestInterface $request, $arguments = null)
    {
        $headerAuth = $request->getServer('HTTP_AUTHORIZATION');
        $auth = [
            'status' => false,
            'data' => []
        ];

        if (!($headerAuth === null || $headerAuth == null)) {

            $headerAuth = explode(' ', $headerAuth);

            // Check if authorization scheme is "Bearer"
            if ($headerAuth[0] == 'Bearer') {

                // Start check JWT Token age
                try {

                    $jwtObject = JWT::decode($headerAuth[1], new Key($this->secret->origin('bearer_key'), 'HS256'));

                    $accUUID = base64_decode($jwtObject->uid_b64);
                    $accData = $this->dbAcc
                        ->selectColumn([
                            'account_id', 'uuid', 'username', 'pav_metaState', 'role', 'privilege'
                        ])
                        ->all([
                            'uuid' => $accUUID,
                            'actived' => true
                        ]);

                    if ($accData != null && count($accData) >= 1) $accData = $accData[0];

                    $member = $this->dbMember
                        ->selectColumn(['member_id'])
                        ->all([
                            'uuid' => $accData['uuid'],
                            'actived' => true,
                            'deleted' => false
                        ]);

                    if ($accData == null || $member == null)
                        throw new Exception();

                    $auth['status'] = true;
                    $auth['data'] = $accData;
                } catch (Exception $exc) {

                    $auth['status'] = false;
                    $auth['data'] = [];
                }
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
