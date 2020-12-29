<?php

use \Taocomp\Einvoicing\FatturaElettronica;
use \Taocomp\Einvoicing\EsitoCommittente;

try
{
    require_once(__DIR__ . '/../vendor/autoload.php');

    $invoice = new FatturaElettronica('FPR12');

    // Set single value
    $invoice->setValue('ProgressivoInvio', random_int(20000,99999));

    // Set multiple values
    $invoice->setValues('IdTrasmittente', array(
        'IdCodice' => '09876543210',
        'IdPaese' => 'IT'
    ));
    $invoice->setValues('CedentePrestatore/IdFiscaleIVA', [
        'IdCodice' => '09876543210',
        'IdPaese' => 'IT',
    ]);
    $invoice->setValues('CessionarioCommittente', array(
        'IdentificativiFiscali/CodiceFiscale' => '01234567890',
        'AltriDatiIdentificativi/Denominazione' => 'BETA SRL'
    ));
    // // Add elements from array
    // $body = $invoice->getBody();
    // $datiGeneraliDocumento = $invoice->getElement('DatiGeneraliDocumento', $body);
    // $invoice->addElementsFromArray($datiGeneraliDocumento, array(
    //     'DatiRitenuta' => array(
    //         'TipoRitenuta' => '',
    //         'ImportoRitenuta' => '23.00',
    //         'AliquotaRitenuta' => ''
    //     )
    // ));

    // // Set values for second body
    // $body2 = $invoice->getBody(2);
    // $invoice->setValue('Numero', 44, $body2);
    // $invoice->setValue('DatiGeneraliDocumento/Data', '2018-12-12', $body2);

    // // Set a big "Causale" in second body
    // $causale = 'Lorem ipsum dolor sit amet, consectetur adipisci elit, sed eiusmod tempor incidunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur. Quis aute iure reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint obcaecat cupiditat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.';
    // $invoice->setValue('DatiGeneraliDocumento/Causale', $causale, $body2);

    // // 2 DatiRiepilogo for second body
    // $invoice->setElementCount('DatiRiepilogo', 2, $body2);
    // $invoice->setValue('DatiRiepilogo[1]/AliquotaIVA', '22.00', $body2);
    // $invoice->setValue('DatiRiepilogo[2]/AliquotaIVA', '10.00', $body2);

    // Save invoice
    // $invoice->save();

    // Show XML
    $xml = $invoice->asXML();
    echo $xml . PHP_EOL;
}
catch (\Exception $e)
{
    echo $e->getMessage() . PHP_EOL;
}
