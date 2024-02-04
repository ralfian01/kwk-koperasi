<?php

namespace App\Filters\Auth;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class APIKey implements FilterInterface
{

    public function before(RequestInterface $request, $arguments = null)
    {



        // $username = isset($_SERVER['PHP_AUTH_USER']) ? $_SERVER['PHP_AUTH_USER'] : false;
        // $password = isset($_SERVER['PHP_AUTH_PW']) ? $_SERVER['PHP_AUTH_PW'] : false;

        // $auth = [
        //     'code' => 200,
        //     'body' => [],
        //     'status' => false,
        // ];

        // if ($username && $password) {

        //     $authentication = new \App\Models\Authentication\BasicAuth();

        //     if (
        //         $username == $authentication->username()
        //         && $password == $authentication->password()
        //     ) {

        //         $auth['status'] = true;
        //     }
        // }

        // if (!$auth['status']) {

        //     $auth['code'] = 401;
        //     $auth['body'] = [
        //         'code' => $auth['code'],
        //         'status' => 'INVALID_AUTHORIZATION',
        //         'description' => 'Basic authorization is required'
        //     ];

        //     $response = service('response');
        //     $response
        //         ->setContentType('application/json')
        //         ->setStatusCode($auth['code'])
        //         ->setBody(json_encode($auth['body'], JSON_PRETTY_PRINT));

        //     return $response;
        // }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // 
    }
}
