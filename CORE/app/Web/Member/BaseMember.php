<?php

namespace App\Web\Member;

use App\Web\BaseWeb;
use Exception;
use App\REST\V1\Member\Get as GetMember;

class BaseMember extends BaseWeb
{
    /**
     * Helpers that will be automatically loaded on class instantiation
     * @var array
     */
    protected $helpers = [
        'array',
        'currency',
        'date',
        'general',
        'text',
        'unit',
        'url_ext',
    ];

    /**
     * Helpers that will be automatically loaded on class instantiation
     * @var string
     */
    protected $baseViewPath = "_Member";


    /**
     * @var array Property that contains the privilege data
     */
    protected $privilegeRules = [];

    /**
     * @var array Property that contains the authentication data
     */
    protected $auth = [];

    /**
     * Member hostname
     * @var string
     */
    protected $memberHostname = "/member";

    /**
     * Page Head
     * @var array|null
     */
    protected $pageHead;


    /**
     * Default function when client unauthorized
     * @return void|string
     */
    protected function __unauthorizedScheme()
    {
        return $this->response->redirect(member_url('login'));
    }

    /**
     * Default function when client does not have valid privilege
     * @return void|string
     */
    protected function __invalidPrivilegeScheme()
    {
        return $this->error(403);
    }

    /**
     * Main activity
     * @return void
     */
    protected function mainActivity()
    {
    }


    public function index()
    {
        $params = func_get_args();

        // Set member URL
        set_member_url($this->memberHostname);

        // Collect authentication data
        $this->auth = $this->request->auth->data ?? [];

        if (isset($this->auth['privilege'])) {

            // // Check account privilege
            if (!$this->checkPrivilege($this->auth['privilege']))
                return $this->__invalidPrivilegeScheme();
        }

        if (!method_exists(self::class, 'mainActivity')) {
            throw new Exception("Method mainActivity() does not exist");
        }

        // Authorize client
        return $this->authHandler(
            fn ($param) => $this->mainActivity(...$param),
            fn () => $this->__unauthorizedScheme(),
            ...$params
        );
    }


    /**
     * @return string
     */
    protected function view(string $path, ?array $data = [])
    {
        $data = array_merge($this->initData(), $data);

        return parent::view($path, $data);
    }

    /**
     * Built-in data initiation
     * @return array
     */
    private function initData()
    {
        $initData = [
            'auth' => $this->auth,
            'layout' => $this->viewLayout(),
            'pageHead' => $this->pageHead
        ];

        return $initData;
    }

    /**
     * Parts of the page layout
     * @return string|null
     */
    protected function viewLayout()
    {
        if (!isset($this->request->auth) || !$this->request->auth->status)
            return null;

        // Try to get member data
        $member = (new GetMember(...['auth' => $this->auth]))->get();

        if (isset($member['code']) && $member['code'] == 404) {
            $member = null;
        }

        return [
            'header' => parent::view('Layout/Components/Header', [
                'pageHead' => $this->pageHead,
                'auth' => $this->auth,
            ]),
            'sidebar' => parent::view('Layout/Components/Sidebar/Sidebar', [
                'member' => $member,
                'auth' => $this->auth,
            ])
        ];
    }


    /**
     * Function to check account authorization
     * @return boolean
     */
    private function checkPrivilege($authority)
    {
        $validCount = 0;
        foreach ($this->privilegeRules as $key => $value) {
            if (in_array($value, $authority)) $validCount++;
        }

        return $validCount >= count($this->privilegeRules);
    }

    /**
     * @param callable  $scFunction     Callback function triggered when process success
     * @param callable  $errFunction    Callback function triggered when process error or failed
     * 
     * @todo callback function $errFunction must be defined if you want to do something to client when they are not authenticated
     * 
     * !!! Note:
     * callback function $errFunction must be defined
     * if you want to do something to client when they
     * are not authenticated
     */
    protected function authHandler(callable $scFunction, callable $errFunction, ...$options)
    {
        // If client not authenticated
        if (isset($this->request->auth)) {

            if (!$this->request->auth->status) {

                if (is_callable($errFunction))
                    return $errFunction($options);
            }

            $this->auth = $this->request->auth->data;

            // If client authenticated
            if (is_callable($scFunction))
                return $scFunction($options);
        } else {

            // If client not authenticated
            if (is_callable($scFunction))
                return $scFunction($options);
        }
    }

    /**
     * Error
     * @return void
     */
    public function error(int $code)
    {
        // Set member URL
        set_member_url($this->memberHostname);

        // Collect authentication data
        $this->auth = $this->request->auth->data ?? [];

        // Reset page head
        $this->pageHead = null;

        return $this->view("Error/{$code}");
    }
}
