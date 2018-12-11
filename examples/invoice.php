<?php

/**
 * Copyright (C) 2018 Taocomp s.r.l.s. <https://taocomp.com>
 *
 * This file is part of php-sdicoop-invoice.
 *
 * php-sdicoop-invoice is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * php-sdicoop-invoice is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with php-sdicoop-invoice.  If not, see <http://www.gnu.org/licenses/>.
 */

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
