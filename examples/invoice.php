<?php

use \Taocomp\EinvoiceIt\FatturaElettronica;
use \Taocomp\EinvoiceIt\EsitoCommittente;

try
{
    require_once(__DIR__ . '/../autoload.php');

    // --------------------------------------------------------------
    // Invoice
    // --------------------------------------------------------------

    // Create a new FPR12 invoice with 2 bodies
    $invoice = new FatturaElettronica('FPR12');
    $invoice->setLotSize(2);
    $invoice->setLineItemCount(3, 2);

    // Set single value
    $invoice->setValue('ProgressivoInvio', random_int(20000,99999));

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

    // Add element
    $invoice->addElement('PECDestinatario', 'DatiTrasmissione');
    $invoice->setValue('PECDestinatario', 'pec@example.com');

    // Set values for second body
    $body2 = $invoice->getBody(2);
    $invoice->setValue('.//Numero', 44, $body2);
    $invoice->setValue('.//Data', '2018-12-12', $body2);

    // Save invoice
    $invoice->save();

    
    // --------------------------------------------------------------
    // Notice
    // --------------------------------------------------------------

    // Create notice
    $notice = new EsitoCommittente();

    // Set some values from invoice
    $notice->setValuesFromInvoice($invoice, 2);

    // Set values
    $notice->setValue('IdentificativoSdI', 1234567);
    $notice->setValue('Esito', EsitoCommittente::EC01);

    // Set filename from invoice
    $notice->setFilenameFromInvoice($invoice, '_EC_001');

    // Save notice
    $notice->save();

}
catch (\Exception $e)
{
    echo $e->getMessage() . PHP_EOL;
}
