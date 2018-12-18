<?php

use \Taocomp\Einvoicing\FatturaElettronica;
use \Taocomp\Einvoicing\Sdicoop\Client;
use \Taocomp\Einvoicing\Sdicoop\FileSdIBase;
use \Taocomp\Einvoicing\Sdicoop\RispostaSdIRiceviFile;

try
{
    // Path to php-sdicoop-client
    define('CLIENT_DIR', __DIR__ . '/../../php-sdicoop-client');
    
    require_once(__DIR__ . '/../vendor/autoload.php');
    require_once(CLIENT_DIR . '/autoload.php');

    // Create a new invoice
    $invoice = new FatturaElettronica('FPR12');
    $invoice->setValue('ProgressivoInvio', random_int(10000, 99999));
    $invoice->setValues('IdTrasmittente', array(
        'IdCodice' => '02313821007',
        'IdPaese' => 'IT'
    ));

    // Setup client
    Client::setPrivateKey(CLIENT_DIR . '/examples/client.key');
    Client::setClientCert(CLIENT_DIR . '/examples/client.pem');
    Client::setCaCert(CLIENT_DIR . '/examples/ca.pem');

    $client = new Client(array(
        'endpoint' => 'https://testservizi.fatturapa.it/ricevi_file',
        'wsdl'     => CLIENT_DIR . '/wsdl/SdIRiceviFile_v1.0.wsdl'
    ));

    // Send invoice
    $fileSdI = new FileSdIBase();
    $fileSdI->load($invoice);
    $response = new RispostaSdIRiceviFile($client->RiceviFile($fileSdI));    
}
catch (\Exception $e)
{
    echo $e->getMessage() . PHP_EOL;
}
