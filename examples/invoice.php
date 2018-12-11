<?php

use \Taocomp\Sdicoop\Invoice;
use \Taocomp\Sdicoop\Notification;

try
{
    // ---------------------------------------------
    // Invoice: create, edit, save and send to SdI
    // ---------------------------------------------
    require_once(__DIR__ . '/../autoload.php');

    // Load templates
    Invoice::addTemplate('FPA12', __DIR__ . '/../templates/FPA12.xml');
    Invoice::addTemplate('FPR12', __DIR__ . '/../templates/FPR12.xml');

    // Company data
    $company = array(
        'Denominazione' => 'ALPHA',
        'IdCodice'      => '01234567890',
        'IdPaese'       => 'IT',
        'RegimeFiscale' => 'RF19',
        'Indirizzo'     => 'VIA DEL TAO 3',
        'CAP'           => '73100',
        'Comune'        => 'LECCE',
        'Provincia'     => 'LE',
        'Nazione'       => 'IT'
    );

    // Create a new FPR12 invoice
    $invoice = Invoice::factory('FPR12', $company);

    // Set invoice data
    $DatiTrasmissione = $invoice->FatturaElettronicaHeader->DatiTrasmissione;
    $DatiTrasmissione->ProgressivoInvio = random_int(10000, 99999);
    $DatiTrasmissione->CodiceDestinatario = '0000000';

    // Save invoice
    $file = $invoice->getNomeFile();
    $invoice->save($file);

    // // Send invoice to SdI
    // require_once('/path/to/php-sdicoop-client/autoload.php');
    // Invoice::setClient(new Client(array(
    //     'endpoint' => 'https://testservizi.fatturapa.it/ricevi_file',
    //     'wsdl'     => CLIENT_DIR . '/wsdl/SdIRiceviFile_v1.0.wsdl'
    // )));
    // $response = $invoice->send();

    // ---------------------------------------------
    // Notifications
    // ---------------------------------------------
    Notification::addTemplate('EC', __DIR__ . '/../templates/EC.xml');

    // Create a notification from invoice
    $notification = $invoice->prepareNotification('EC');

    // Edit data
    $notification->IdentificativoSdI = 1010101;

    // Save to file
    $notifFile = basename($file, '.xml') . '_EC_001.xml';
    $notification->save($notifFile);

    // Send to SdI
    // require_once('/path/to/php-sdicoop-client/autoload.php');
    // Notification::setClient(new Client(array(
    //     'endpoint' => 'https://testservizi.fatturapa.it/ricevi_notifica',
    //     'wsdl'     => CLIENT_DIR . '/wsdl/SdIRiceviNotifica_v1.0.wsdl'
    // )));
    // $response = $notification->send();
}
catch (\Exception $e)
{
    echo $e->getMessage() . PHP_EOL;
}
