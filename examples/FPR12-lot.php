<?php

use Taocomp\Einvoicing\FatturaElettronica;

try
{
    require_once(__DIR__ . '/../vendor/autoload.php');

    // sample data
    require_once(__DIR__ . '/data.php');

    $invoice = new FatturaElettronica('FPR12');

    // 2 bodies (lot with 2 invoices)
    $invoice->setBodyCount(2);

    // 2 "dettaglio linee" for body 1
    // 3 "dettaglio linee" for body 2
    $lineItemCount = array(2, 3);
    $invoice->setLineItemCount($lineItemCount[0], 1);
    $invoice->setLineItemCount($lineItemCount[1], 2);

    // Dati Trasmissione
    $invoice->setValues('DatiTrasmissione', $DatiTrasmissione);
    $invoice->setValue('ProgressivoInvio', '00002');
    $invoice->setValue('CodiceDestinatario', '0000000');
    $invoice->setValue('PECDestinatario', 'betagamma@pec.it');

    // Cedente Prestatore
    $invoice->setValues('CedentePrestatore', $CedentePrestatore);

    // Cessionario Committente
    $invoice->setValues('CessionarioCommittente', $CessionarioCommittente);

    // Bodies
    $bodies = $invoice->getBodies();
    foreach ($bodies as $k => $body) {
        // Dati Generali Documento
        $invoice->setValues('DatiGeneraliDocumento', $DatiGeneraliDocumento, $body);
        $invoice->setValue('DatiGeneraliDocumento/Numero', $k + 100, $body);

        // Dati Ordine Acquisto
        $invoice->setValues('DatiOrdineAcquisto', $DatiOrdineAcquisto, $body);

        // Dati Trasporto
        $invoice->setValues('DatiTrasporto', $DatiTrasporto, $body);
        
        // Dettaglio Linee
        for ($i = 1; $i <= $lineItemCount[$k]; $i++ ) {
            $invoice->setValues("DettaglioLinee[$i]", $DettaglioLinee[$i], $body);
        }

        // Dati Riepilogo
        $invoice->setValues('DatiRiepilogo', $DatiRiepilogo, $body);

        // Dati Pagamento
        $invoice->setValues('DatiPagamento', $DatiPagamento, $body);
    }

    // save invoice
    $invoice->setPrefixPath(__DIR__)->save(true);

    // show xml
    $xml = $invoice->asXML();
    header("Content-type: text/xml; charset=utf-8");
    echo $xml . PHP_EOL;
}
catch (\Exception $e)
{
    echo $e->getMessage() . PHP_EOL;
}
