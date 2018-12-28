<?php

$DatiTrasmissione = array(
    'IdPaese'  => 'IT',
    'IdCodice' => '02313821007'
);

$CedentePrestatore = array(
    'IdPaese'        => 'IT',
    'IdCodice'       => '02313821007',
    'Denominazione'  => "SOCIETA' ALPHA SRL",
    'RegimeFiscale'  => 'RF01',
    'Sede/Indirizzo' => 'VIALE ROMA 543',
    'Sede/CAP'       => '07100',
    'Sede/Comune'    => "SASSARI",
    'Sede/Provincia' => 'SS',
    'Sede/Nazione'   => 'IT'
);

$CessionarioCommittente = array(
    'CodiceFiscale'  => '02313821007',
    'Anagrafica/Denominazione' => 'BETA GAMMA',
    'Sede/Indirizzo' => 'VIA TORINO 38-B',
    'Sede/CAP'       => '00145',
    'Sede/Comune'    => "ROMA",
    'Sede/Provincia' => 'RM',
    'Sede/Nazione'   => 'IT'
);

$DatiGeneraliDocumento = array(
    'TipoDocumento' => 'TD01',
    'Divisa'        => 'EUR',
    'Data'          => '2018-12-12',
    'Numero'        => '123',
    'Causale'       => 'Causale'
);

$DatiOrdineAcquisto = array(
    'RiferimentoNumeroLinea' => '1',
    'IdDocumento' => '66685',
    'NumItem'     => '1'
);

$DatiTrasporto = array(
    'IdPaese'         => 'IT',
    'IdCodice'        => '24681012141',
    'Denominazione'   => 'Trasporto spa',
    'DataOraConsegna' => '2012-10-22T16:46:12.000+02:00'
);

$DettaglioLinee = array();
for ($i = 1; $i < 6; $i++) {
    $DettaglioLinee[$i] = array(
        'NumeroLinea'    => $i,
        'Descrizione'    => "Descrizione $i",
        'Quantita'       => '5.00',
        'PrezzoUnitario' => '1.00',
        'PrezzoTotale'   => '5.00',
        'AliquotaIVA'    => '22.00'
    );
}

$DatiRiepilogo = array(
    'AliquotaIVA'       => '22.00',
    'ImponibileImporto' => '25.00',
    'Imposta'           => '5.50',
    'EsigibilitaIVA'    => 'D'
);

$DatiPagamento = array(
    'CondizioniPagamento'   => 'TP01',
    'ModalitaPagamento'     => 'MP01',
    'DataScadenzaPagamento' => '2018-12-31',
    'ImportoPagamento'      => '30.50'
);
