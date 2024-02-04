<?php

namespace AppConfig;

class TopSecret
{

    public function __construct()
    {

        return $this;
    }

    /**
     * --------------------------------------------------------------------------
     * ORIGIN SECRET
     * --------------------------------------------------------------------------
     *
     * Origin Secret
     */

    public $origin = [
        'bypass'        => 'q253b-56b46bqq253-25bq2-2we',
        'api_key'       => 'aGVsbG93b3JsZF8xMjMxMjM',
        'bearer_key'    => 'q3b0495b8yqn3095qb8y24n60948y9028y0359r8vuyopijn0-9ui-052u309n8y20uq5v46u670',
        'basic_auth'    => [ // Basic auth by pass
            'user'      => 'myu_109y834r232',
            'pass'      => 'myu_q09q83bt4n9y33w'
        ]
    ];

    public function origin($name = null)
    {

        if ($name == null) return $this->origin;

        foreach ($this->origin as $key => $val) {

            if ($key == $name) return $val;
        }
    }

    /**
     * --------------------------------------------------------------------------
     * THIRD PARTY SECRET
     * --------------------------------------------------------------------------
     *
     * Third Party Secret
     */

    public $third_party = [
        'google'        => [
            'api_key'   => '',
            '2_oauth'   => [
                'client_id'     => '',
                'client_secret' => ''
            ]
        ],
    ];

    public function thirdParty($name = null)
    {

        if ($name == null) return $this->third_party;

        foreach ($this->third_party as $key => $val) {

            if ($key == $name) return $val;
        }
    }
}
