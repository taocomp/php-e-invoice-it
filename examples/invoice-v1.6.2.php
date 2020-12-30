<?php

// The XML generated from this PHP script has been correctly validated by SdI
// (https://ivaservizi.agenziaentrate.gov.it/ser/fatturewizard/#/controllo)

use \Taocomp\Einvoicing\FatturaElettronica;
use \Taocomp\Einvoicing\EsitoCommittente;

try
{
    require_once(__DIR__ . '/../vendor/autoload.php');

    // sample data
    require_once(__DIR__ . '/data.php');

    $invoice = new FatturaElettronica('FPR12');

    $lineItemCount = 5;
    $invoice->setLineItemCount($lineItemCount);

    // Dati Trasmissione
    $invoice->setValues('DatiTrasmissione', $DatiTrasmissione);
    $invoice->setValue('ProgressivoInvio', '00001');
    $invoice->setValue('CodiceDestinatario', '0000000');
    $invoice->setValue('PECDestinatario', 'betagamma@pec.it');

    // Cedente Prestatore
    $invoice->setValues('CedentePrestatore', $CedentePrestatore);

    // Cessionario Committente
    $invoice->setValues('CessionarioCommittente', $CessionarioCommittente);

    // Dati Generali Documento
    $invoice->setValues('DatiGeneraliDocumento', $DatiGeneraliDocumento);

    // Dati Ordine Acquisto
    $invoice->setValues('DatiOrdineAcquisto', $DatiOrdineAcquisto);

    // Dati Trasporto
    $invoice->setValues('DatiTrasporto', $DatiTrasporto);

    // Dettaglio Linee
    for ($i = 1; $i <= $lineItemCount; $i++ ) {
        $invoice->setValues("DettaglioLinee[$i]", $DettaglioLinee[$i]);
    }

    // Dati Riepilogo
    $invoice->setValues('DatiRiepilogo', $DatiRiepilogo);

    // Dati Pagamento
    $invoice->setValues('DatiPagamento', $DatiPagamento);

    $xml = $invoice->asXML();
    echo $xml . PHP_EOL;
}
catch (\Exception $e)
{
    echo $e->getMessage() . PHP_EOL;
}
