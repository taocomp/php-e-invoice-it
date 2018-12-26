<?php

use Taocomp\Einvoicing\FatturaElettronica;

try
{
    require_once(__DIR__ . '/../vendor/autoload.php');

    $invoice = new FatturaElettronica('FPR12');
    $invoice->setLineItemCount(2);

    // Dati Trasmissione
    $invoice->setValues('DatiTrasmissione', array(
        'IdPaese' => 'IT',
        'IdCodice' => '02313821007',
        'ProgressivoInvio' => '00001',
        'CodiceDestinatario' => '0000000',
        'PECDestinatario' => 'betagamma@pec.it'
    ));

    // Cedente Prestatore
    $invoice->setValues('CedentePrestatore', array(
        'IdPaese' => 'IT',
        'IdCodice' => '02313821007',
        'Denominazione' => "SOCIETA' ALPHA SRL",
        'RegimeFiscale' => 'RF01'
    ));
    $invoice->setValues('CedentePrestatore/Sede', array(
        'Indirizzo' => 'VIALE ROMA 543',
        'CAP' => '07100',
        'Comune' => "SASSARI",
        'Provincia' => 'SS',
        'Nazione' => 'IT'
    ));

    // Cessionario Committente
    $invoice->setValues('CessionarioCommittente', array(
        'CodiceFiscale' => '02313821007',
        'Anagrafica/Denominazione' => 'BETA GAMMA'
    ));
    $invoice->setValues('CessionarioCommittente/Sede', array(
        'Indirizzo' => 'VIA TORINO 38-B',
        'CAP' => '00145',
        'Comune' => "ROMA",
        'Provincia' => 'RM',
        'Nazione' => 'IT'
    ));

    // Dati Generali Documento
    $invoice->setValues('DatiGeneraliDocumento', array(
        'TipoDocumento' => 'TD01',
        'Divisa' => 'EUR',
        'Data' => '2018-12-12',
        'Numero' => '123',
        'Causale' => 'Causale'
    ));

    // Dati Ordine Acquisto
    $invoice->setValues('DatiOrdineAcquisto', array(
        'RiferimentoNumeroLinea' => '1',
        'IdDocumento' => '66685',
        'NumItem' => '1'
    ));

    // Dati Trasporto
    $invoice->setValues('DatiTrasporto', array(
        'IdPaese' => 'IT',
        'IdCodice' => '24681012141',
        'Denominazione' => 'Trasporto spa',
        'DataOraConsegna' => '2012-10-22T16:46:12.000+02:00'
    ));

    // Dettaglio Linee 1
    $invoice->setValues("DettaglioLinee[1]", array(
        'NumeroLinea' => '1',
        'Descrizione' => 'Descrizione',
        'Quantita' => '5.00',
        'PrezzoUnitario' => '1.00',
        'PrezzoTotale' => '5.00',
        'AliquotaIVA' => '22.00'
    ));
    
    // Dettaglio Linee 2
    $invoice->setValues("DettaglioLinee[2]", array(
        'NumeroLinea' => '2',
        'Descrizione' => 'FORNITURE VARIE PER UFFICIO',
        'Quantita' => '10.00',
        'PrezzoUnitario' => '2.00',
        'PrezzoTotale' => '20.00',
        'AliquotaIVA' => '22.00'
    ));

    // Dati Riepilogo
    $invoice->setValues('DatiRiepilogo', array(
        'AliquotaIVA' => '22.00',
        'ImponibileImporto' => '25.00',
        'Imposta' => '5.50',
        'EsigibilitaIVA' => 'D'
    ));

    // Dati Pagamento
    $invoice->setValues('DatiPagamento', array(
        'CondizioniPagamento' => 'TP01',
        'ModalitaPagamento' => 'MP01',
        'DataScadenzaPagamento' => '2018-12-31',
        'ImportoPagamento' => '30.50'
    ));

    $invoice->setPrefixPath(__DIR__)->save(true);
    
    $xml = $invoice->asXML();
    header("Content-type: text/xml; charset=utf-8");
    echo $xml . PHP_EOL;
}
catch (\Exception $e)
{
    echo $e->getMessage() . PHP_EOL;
}
