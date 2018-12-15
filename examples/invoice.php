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

use \Taocomp\EinvoiceIt\FatturaElettronica;
use \Taocomp\EinvoiceIt\NotificaEsitoCommittente;

try
{
    require_once(__DIR__ . '/../autoload.php');

    // --------------------------------------------------------------
    // Invoice
    // --------------------------------------------------------------

    // Create a new FPR12 invoice with 2 bodies
    $invoice = new FatturaElettronica('FPR12');
    $invoice->setBatchSize(2);
    $invoice->setLineItemCount(3, 2);

    // Set single value
    $invoice->setValue('ProgressivoInvio', 10001);

    // Set multiple values
    $invoice->setValues('IdTrasmittente', array(
        'IdCodice' => '09876543210',
        'IdPaese' => 'IT'
    ));
    $invoice->setValues('CedentePrestatore/Sede', array(
        'Indirizzo' => 'VIA UNIVERSO 1'
    ));
    $invoice->setValues('CessionarioCommittente', array(
        // CessionarioCommittente/DatiAnagrafici/CodiceFiscale
        'DatiAnagrafici/CodiceFiscale' => '01234567890',
        // Denominazione, somewhere inside CessionarioCommittente
        './/Denominazione' => 'BETA SRL'
    ));

    // Set values for second body
    $body2 = $invoice->getBody(2);
    $invoice->setValue('.//Numero', 44, $body2);
    $invoice->setValue('.//Data', '2018-12-12', $body2);

    // Save invoice
    $invoice->save();

    
    // --------------------------------------------------------------
    // Notification
    // --------------------------------------------------------------

    // Create notification
    $notification = new NotificaEsitoCommittente();

    // Set some values from invoice
    $notification->setValuesFromInvoice($invoice, 2);

    // Set values
    $notification->setValue('IdentificativoSdI', 1234567);
    $notification->setValue('Esito', NotificaEsitoCommittente::EC01);

    // Set filename from invoice
    $notification->setFilenameFromInvoice($invoice, '_EC_001');

    // Save notification
    $notification->save();

}
catch (\Exception $e)
{
    echo $e->getMessage() . PHP_EOL;
}
