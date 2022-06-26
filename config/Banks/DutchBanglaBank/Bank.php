<?php

return [

    // Global settings
    'bid' => 'DBBL',
    'javaPath' => 'java',
    'dbblJarPath' => "ecomm_merchant.jar",
    'dbblPropertyFilePath' => "merchant.properties",

    // Sandbox settings
    'sandbox' => array(
        'ecomPath' => '/home/paysenz/dbbl-ecom-sandbox',
        'merchantName' => "PAYSZ-Bangladesh_Navy",
        'submerchantId' => "000599001080001",
        'terminalId' => "59901572",
        'dbblpan' => "123456",
        'url' => 'https://ecomtest.dutchbanglabank.com/ecomm2/ClientHandler',
    ),

    // Live settings
    'live' => array(
        'ecomPath' => '/home/paysenz/dbbl-ecom-live',
        'merchantName' => "PAYSZ-Bangladesh_Navy", // Not required for LIVE
        'submerchantId' => "000599001080001", // Not required for LIVE
        'terminalId' => "59901572", // Not required for LIVE
        'dbblpan' => "123456", // Not required for LIVE
        'url' => 'https://ecom1.dutchbanglabank.com/ecomm2/ClientHandler',
    )
];
