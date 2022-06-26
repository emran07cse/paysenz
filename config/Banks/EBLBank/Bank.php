<?php

return [
    // Sandbox settings
    'sandbox' => array(
        // possible values:
        // FALSE = test mode
        // TRUE = live mode
        'gatewayMode' => FALSE,
        
        
        // possible values:
        // FALSE = disable verification
        // TRUE = enable verification
        'certificateVerifyPeer' => FALSE,
        
        // possible values:
        // 0 = do not check/verify hostname
        // 1 = check for existence of hostname in certificate
        // 2 = verify request hostname matches certificate hostname
        'certificateVerifyHost' => 0,
        
        // Merchant ID supplied by your payments provider
        'merchantId' => 'TEST20070005',
        
        // API password which can be configured in Merchant Administration
        'password' => '05fccee3c64994c853c453f32b3b0e09',
        
        // The debug setting controls displaying the raw content of the request and response for a transaction.
        // In production you should ensure this is set to FALSE as to not display/use this debugging information
        'debug' => FALSE,
        
        // Version number of the API being used for your integration this is the default value if it isn't being specified in process.php
        'version' => "41"
    ),
    
    
    // Live settings
    'live' => array(
        // possible values:
        // FALSE = test mode
        // TRUE = live mode
        'gatewayMode' => TRUE,
        
        
        // possible values:
        // FALSE = disable verification
        // TRUE = enable verification
        'certificateVerifyPeer' => FALSE,
        
        // possible values:
        // 0 = do not check/verify hostname
        // 1 = check for existence of hostname in certificate
        // 2 = verify request hostname matches certificate hostname
        'certificateVerifyHost' => 0,
        
        // Merchant ID supplied by your payments provider
        'merchantId' => '40500003',
        
        // API password which can be configured in Merchant Administration
        'password' => '515691bec3d57ed77b655e79025654ba',
        
        // The debug setting controls displaying the raw content of the request and response for a transaction.
        // In production you should ensure this is set to FALSE as to not display/use this debugging information
        'debug' => FALSE,
        
        // Version number of the API being used for your integration this is the default value if it isn't being specified in process.php
        'version' => "41"
    )
];
