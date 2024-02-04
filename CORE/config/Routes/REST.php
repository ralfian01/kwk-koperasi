<?php

use MVCME\Router\Static\REST;
use App\REST as RESTPage;
use App\REST\V1 as RESTV1;

/*
 * ---------------------------------------------------
 * NEW AGE
 * ---------------------------------------------------
 */

## Authentication
REST::group('auth', function ($rest) {

    // Authenticate client using username/email and password
    $rest->post('account', [RESTV1\Auth\LoginAccount::class, 'index']);

    // // Authenticate client using google callback
    // $rest->post('google', [RESTV1\Auth\LoginGoogle::class, 'index']);
});

## My Account
REST::group('account', ['middleware' => 'Auth.Bearer.Active'], function ($rest) {

    // Get my account data
    $rest->get('/', [RESTV1\Account\Get::class, 'index']);

    // Delete my account data
    $rest->delete('/', [RESTV1\Account\Delete::class, 'index']);

    ### My Account security
    $rest->group('security', function ($rest) {

        ### Update my account password
        $rest->group('password', function ($rest) {

            // Request update password token
            $rest->put('/', [RESTV1\Account\Security\Password\Request::class, 'index']);

            // Update password token
            $rest->post('/', [RESTV1\Account\Security\Password\Confirm::class, 'index']);
        });
    });
});

## My Member
REST::group('member', ['middleware' => 'Auth.Bearer.Active'], function ($rest) {

    // Get my account data
    $rest->get('/', [RESTV1\Member\Get::class, 'index']);

    ### Delete my member data
    $rest->group('delete', ['middleware' => 'Auth.Bearer.Member.Active'], function ($rest) {

        // Request Delete my member data
        $rest->delete('/', [RESTV1\Member\Delete\Request::class, 'index']);

        // Cancel delete my member data
        $rest->put('/', [RESTV1\Member\Delete\Cancel::class, 'index']);
    });

    ### Update my member data
    // Update member data
    $rest->put('/', [RESTV1\Member\Update\Member::class, 'index']);

    // Update member identity data
    $rest->put('identity', [RESTV1\Member\Update\Identity::class, 'index']);

    // Update member business data
    $rest->put('business', [RESTV1\Member\Update\Business::class, 'index']);

    ### My member pay deposit
    $rest->group('deposit', ['middleware' => 'Auth.Bearer.Member.Active'], function ($rest) {

        // Get my deposit list
        $rest->get('/', [RESTV1\Member\Deposit\Get::class, 'index'], ['middleware' => 'Auth.Bearer.Active']);
        $rest->get('(:segment)', [RESTV1\Member\Deposit\Get::class, 'index'], [
            'placeholder' => '$1:id',
            'middleware' => 'Auth.Bearer.Active'
        ]);

        // Request pay deposit
        $rest->put('/', [RESTV1\Member\Deposit\Request::class, 'index']);

        // Cancel Request pay deposit
        $rest->delete('/', [RESTV1\Member\Deposit\Cancel::class, 'index']);
        $rest->delete('(:segment)', [RESTV1\Member\Deposit\Cancel::class, 'index'], ['placeholder' => '$1:id']);

        // Pay deposit
        $rest->post('/', [RESTV1\Member\Deposit\Pay::class, 'index']);
        $rest->post('(:segment)', [RESTV1\Member\Deposit\Pay::class, 'index'], ['placeholder' => '$1:id']);
    });
});

## Registration
REST::group('registration', function ($rest) {

    ### Register account using username/email and password
    $rest->group('account', function ($rest) {

        // Register new account
        $rest->post('/', [RESTV1\Registration\Account\Insert::class, 'index']);

        // Confirm registration using link that sended to email
        $rest->patch('confirmation', [RESTV1\Registration\Account\Confirm::class, 'index']);
    });

    ### Register membership
    $rest->group('member', ['middleware' => 'Auth.Bearer.Active'], function ($rest) {

        // Register new member
        $rest->post('/', [RESTV1\Registration\Member\Insert::class, 'index']);

        // Cancel register new member
        $rest->delete('/', [RESTV1\Registration\Member\Cancel::class, 'index']);

        // Pay new member register
        $rest->post('pay', [RESTV1\Registration\Member\Pay::class, 'index']);
    });
});

## Manage by manager
REST::group('manage', ['middleware' => 'Auth.Bearer.Member.Active'], function ($rest) {

    ### Manage member data
    $rest->group('member', function ($rest) {

        // Show list of active member
        $rest->get('/', [RESTV1\Manage\Member\Get::class, 'index']);

        // Update member data manually
        $rest->put('manual/(:segment)', [RESTV1\Manage\Member\ManualUpdate::class, 'index'], ['placeholder' => '$1:id']);

        #### Manage new member register
        $rest->group('register', function ($rest) {

            // Show list of new member that needs confirmation
            $rest->get('/', [RESTV1\Manage\Member\Register\Get::class, 'index']);
            $rest->get('(:segment)', [RESTV1\Manage\Member\Register\Get::class, 'index'], ['placeholder' => '$1:id']);

            // Confirm new member registration
            $rest->put('confirm', [RESTV1\Manage\Member\Register\Confirm::class, 'index']);
            $rest->put('confirm/(:segment)', [RESTV1\Manage\Member\Register\Confirm::class, 'index'], ['placeholder' => '$1:id']);

            // Confirm new member registration payment
            $rest->put('confirm_payment', [RESTV1\Manage\Member\Register\ConfirmPayment::class, 'index']);
            $rest->put('confirm_payment/(:segment)', [RESTV1\Manage\Member\Register\ConfirmPayment::class, 'index'], ['placeholder' => '$1:id']);

            // Input member data manually
            $rest->post('manual', [RESTV1\Manage\Member\Register\ManualInput::class, 'index']);
        });

        #### Manage member deposit payment
        $rest->group('deposit_payment', function ($rest) {

            // Show list of deposit payment that need verification
            $rest->get('/', [RESTV1\Manage\Member\DepositPayment\Get::class, 'index']);
            $rest->get('(:segment)', [RESTV1\Manage\Member\DepositPayment\Get::class, 'index'], ['placeholder' => '$1:id']);

            // Confirm deposit payment that need verification
            $rest->put('confirm', [RESTV1\Manage\Member\DepositPayment\Confirm::class, 'index']);
            $rest->put('confirm/(:segment)', [RESTV1\Manage\Member\DepositPayment\Confirm::class, 'index'], ['placeholder' => '$1:id']);
        });
    });

    ### Manage finance report
    $rest->group('finance_report', function ($rest) {

        #### Finance report income
        $rest->group('income', function ($rest) {

            // Show list of finance report income
            $rest->get('/', [RESTV1\Manage\FinanceReport\Income\Get::class, 'index']);
            $rest->get('(:segment)', [RESTV1\Manage\FinanceReport\Income\Get::class, 'index'], ['placeholder' => '$1:id']);

            // Insert new finance report income
            $rest->post('/', [RESTV1\Manage\FinanceReport\Income\Insert::class, 'index']);

            // Delete finance report income
            $rest->delete('/', [RESTV1\Manage\FinanceReport\Income\Delete::class, 'index']);
            $rest->delete('(:segment)', [RESTV1\Manage\FinanceReport\Income\Delete::class, 'index'], ['placeholder' => '$1:id']);
        });

        #### Finance report outcome
        $rest->group('outcome', function ($rest) {

            // Show list of finance report outcome
            $rest->get('/', [RESTV1\Manage\FinanceReport\Outcome\Get::class, 'index']);
            $rest->get('(:segment)', [RESTV1\Manage\FinanceReport\Outcome\Get::class, 'index'], ['placeholder' => '$1:id']);

            // Insert new finance report outcome
            $rest->post('/', [RESTV1\Manage\FinanceReport\Outcome\Insert::class, 'index']);

            // Delete finance report outcome
            $rest->delete('/', [RESTV1\Manage\FinanceReport\Outcome\Delete::class, 'index']);
            $rest->delete('(:segment)', [RESTV1\Manage\FinanceReport\Outcome\Delete::class, 'index'], ['placeholder' => '$1:id']);
        });
    });
});

// REST::get('(:segment)', [RESTV1\Home::class, 'index'], ['placeholder' => '$1:uuid']);

// Override 404 page
REST::setDefault404([RESTPage\Errors\Error404::class, 'index']);
