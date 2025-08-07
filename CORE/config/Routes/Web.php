<?php

use MVCME\Router\Static\Web;
use App\Web\Home;
use App\Web\Member as Member;

/*
 * ---------------------------------------------------
 * NEW AGE
 * ---------------------------------------------------
 */

## Commercial
Web::group('/', function ($web) {

    // $web->get('/', function ($web) {
    //     return header('Location: youtube.com');
    // });
});

## Member
Web::group('member', ['middleware' => 'Auth.CookieToken'], function ($web) {

    ### Member Authentication
    // Login
    $web->get('login', [Member\Auth\Login::class, 'index']);

    // Logout
    $web->get('logout', [Member\Auth\Logout::class, 'index']);

    // Register
    $web->get('register', [Member\Auth\Register::class, 'index']);

    ### Member Recovery
    // Reset password
    $web->get('reset_password', [Member\Recovery\ResetPassword::class, 'index']);

    ### Member Control Page
    // Dashboard page
    $web->get('/', [Member\InformationSystem\Dashboard::class, 'index']);

    ### My account page
    $web->group('my_account', function ($web) {

        // Get member list
        $web->get('setting', [Member\InformationSystem\MyAccount\Setting::class, 'index']);
    });

    ### Membership
    $web->group('membership', function ($web) {

        // Membership page
        $web->get('/', [Member\InformationSystem\Membership\Membership::class, 'index']);

        // Membership registration page
        $web->get('register', [Member\InformationSystem\Membership\Registration::class, 'index']);

        #### Membership setting page
        $web->group('setting', function ($web) {

            // Show all membership settings
            $web->get('/', [Member\InformationSystem\Membership\Setting::class, 'index']);

            // // Show all membership settings
            // $web->get('/', [Member\InformationSystem\Membership\Setting::class, 'index']);

            // // Show all membership settings
            // $web->get('/', [Member\InformationSystem\Membership\Setting::class, 'index']);
        });

        #### Member deposit
        $web->group('deposit', function ($web) {

            // Request new member deposit
            $web->get('new', [Member\InformationSystem\Membership\Deposit\NewData::class, 'index']);

            // List of member deposit page
            $web->get('/', [Member\InformationSystem\Membership\Deposit\Lists::class, 'index']);
            $web->get('(:segment)', [Member\InformationSystem\Membership\Deposit\Detail::class, 'index']);
        });
    });


    ### Manage
    $web->group('manage', function ($web) {

        #### Manage member
        $web->group('member', function ($web) {

            // Get new member list
            $web->get('new', [Member\InformationSystem\Manage\Member\NewMember\Lists::class, 'index']);
            $web->get('new/(:segment)', [Member\InformationSystem\Manage\Member\NewMember\Detail::class, 'index']);

            // Manual input
            $web->get('manual_input', [Member\InformationSystem\Manage\Member\NewMember\ManualInput::class, 'index']);
            $web->get('manual_input/(:segment)', [Member\InformationSystem\Manage\Member\Active\ManualUpdate::class, 'index']);

            // Get member list
            $web->get('/', [Member\InformationSystem\Manage\Member\Active\Lists::class, 'index']);
            $web->get('(:segment)', [Member\InformationSystem\Manage\Member\Active\Detail::class, 'index']);
        });

        #### Manage deposit
        $web->group('deposit', function ($web) {

            // Get active deposit list
            $web->get('/', [Member\InformationSystem\Manage\Deposit\Lists::class, 'index']);

            // Get new member list
            $web->get('(:segment)', [Member\InformationSystem\Manage\Deposit\Detail::class, 'index']);
        });

        #### Manage finance report
        $web->group('finance_report', function ($web) {

            ##### Manage finance report income
            $web->group('income', function ($web) {

                // Get finance report income list
                $web->get('/', [Member\InformationSystem\Manage\FinanceReport\Income\Lists::class, 'index']);

                // Insert finance report income list
                $web->get('new', [Member\InformationSystem\Manage\FinanceReport\Income\NewData::class, 'index']);
            });

            ##### Manage finance report outcome
            $web->group('outcome', function ($web) {

                // Get finance report outcome list
                $web->get('/', [Member\InformationSystem\Manage\FinanceReport\Outcome\Lists::class, 'index']);

                // Insert finance report outcome list
                $web->get('new', [Member\InformationSystem\Manage\FinanceReport\Outcome\NewData::class, 'index']);
            });
        });
    });


    ### 404 Page
    $web->get('(:any)', [Member\BaseMember::class, 'error/404']);
});

Web::get('/', [Home::class, 'index']);
