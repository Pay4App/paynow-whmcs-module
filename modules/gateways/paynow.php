<?php
/**
 * WHMCS PayNow Payment Gateway Module
 *
 * @see http://sam.co.zw/paynow-whmcs-module
 *
 * @copyright Copyright (c) Sam Takunda 2016
 */

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

require 'paynow/vendor/autoload.php';

/**
 * Define module related meta data.
 *
 * Values returned here are used to determine module related capabilities and
 * settings.
 *
 * @see http://docs.whmcs.com/Gateway_Module_Meta_Data_Parameters
 *
 * @return array
 */
function paynow_MetaData()
{
    return array(
        'DisplayName' => 'Paynow',
        'APIVersion' => '1.1', // Use API Version 1.1
        'DisableLocalCredtCardInput' => true,
        'TokenisedStorage' => false,
    );
}

/**
 * Gateway configuration options.
 *
 * @return array
 */
function paynow_config()
{
    return array(
        // the friendly display name for a payment gateway should be
        // defined here for backwards compatibility
        'FriendlyName' => array(
            'Type' => 'System',
            'Value' => 'Paynow',
        ),
        // a text field type allows for single line text input
        'integrationId' => array(
            'FriendlyName' => 'Integration Id',
            'Type' => 'text',
            'Size' => '25',
            'Default' => '',
            'Description' => 'Enter your Paynow integration Id here',
        ),
        // a password field type allows for masked text input
        'integrationKey' => array(
            'FriendlyName' => 'Integration key',
            'Type' => 'password',
            'Size' => '100',
            'Default' => '',
            'Description' => 'Enter your Paynow integration key here',
        ),
    );
}

/**
 * Payment link.
 *
 * Required by third party payment gateway modules only.
 *
 * Defines the HTML output displayed on an invoice. Typically consists of an
 * HTML form that will take the user to the payment gateway endpoint.
 *
 * @param array $params Payment Gateway Module Parameters
 *
 * @see http://docs.whmcs.com/Payment_Gateway_Module_Parameters
 *
 * @return string
 */
function paynow_link($params)
{
    // Gateway Configuration Parameters
    $integrationId = $params['integrationId'];
    $integrationKey = $params['integrationKey'];

    // Invoice Parameters
    $invoiceId = $params['invoiceid'];
    $description = $params["description"];
    $amount = $params['amount'];
    $currencyCode = $params['currency'];

    // System Parameters    
    $moduleName = $params['paymentmethod'];
    $systemUrl = $params['systemurl'];
    $returnUrl = $params['returnurl'];
    $langPayNow = $params['langpaynow'];

    $paynow = new Paynow\Paynow($integrationId, $integrationKey);

    $resultUrl = $systemUrl . '/modules/gateways/callback/' . $moduleName . '.php';

    $transaction = $paynow->initiatePayment(
        $reference = $invoiceId,
        $amount = $amount,
        $additionalInfo = $description,
        $returnUrl = $returnUrl,
        $resultUrl = $resultUrl
    );

    return "<div><a class='btn btn-primary' href='".$transaction->browserurl."'>" . $langPayNow . "</a></div>";
}

