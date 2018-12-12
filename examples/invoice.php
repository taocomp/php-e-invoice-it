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
    require_once(__DIR__ . '/../autoload.php');

    // Add some invoice templates
    Invoice::addTemplate('FPA12', __DIR__ . '/../templates/FPA12.xml');
    Invoice::addTemplate('FPR12', __DIR__ . '/../templates/FPR12.xml');

    // Create a new FPR12 invoice
    $invoice = Invoice::factory('FPR12');

    // Set some invoice data
    $invoice->setCompanyData(array(
        'Denominazione' => 'ALPHA',
        'IdCodice'      => '01234567890',
        'IdPaese'       => 'IT',
        'RegimeFiscale' => 'RF19',
        'Indirizzo'     => 'VIA DEL TAO 3',
        'CAP'           => '73100',
        'Comune'        => 'LECCE',
        'Provincia'     => 'LE',
        'Nazione'       => 'IT'
    ));
    $invoice->setPECDestinatario('pec@example.com');
    $DT = $invoice->FatturaElettronicaHeader->DatiTrasmissione;
    $DT->ProgressivoInvio = random_int(10000, 99999);
    $DT->CodiceDestinatario = '0000000';

    // Save invoice
    $invoice->save(__DIR__);
    // or
    // Invoice::setDestinationDir(__DIR__);
    // $invoice->save();

    // Add a notification template
    Notification::addTemplate('EC', __DIR__ . '/../templates/EC.xml');

    // Create a notification from invoice
    $notification = $invoice->prepareNotification('EC');
    // or an empty one:
    // $notification = Notification::factory('EC');

    // Edit data
    $notification->IdentificativoSdI = 1010101;
    $notification->Descrizione = '';

    // Save to file
    $notificationFile = basename($invoice->getFilename(), '.xml')
                      . '_EC_001.xml';
    $notification->save($notificationFile, true);
}
catch (\Exception $e)
{
    echo $e->getMessage() . PHP_EOL;
}
