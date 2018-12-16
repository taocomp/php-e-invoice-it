<?php

/**
 * Copyright (C) 2018 Taocomp s.r.l.s. <https://taocomp.com>
 *
 * This file is part of php-e-invoice-it.
 *
 * php-e-invoice-it is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * php-e-invoice-it is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with php-e-invoice-it.  If not, see <http://www.gnu.org/licenses/>.
 */

use \Taocomp\EinvoiceIt\Invoice;

try
{
    require_once(__DIR__ . '/../autoload.php');

    $invoice = new Invoice('FPA12');

    $invoice->setValue('ProgressivoInvio', 10001);
    $invoice->setValue('CodiceDestinatario', '999999');

    $invoice->setValues('IdTrasmittente', array(
        'IdCodice' => '02313821007',
        'IdPaese' => 'IT'
    ));
    $invoice->setValues('CedentePrestatore', array(
        './/IdPaese' => 'IT',
        './/IdCodice' => '02313821007',
        './/Denominazione' => 'CEDENTE SRL',
        './/RegimeFiscale' => 'RF19',
    ));
    $invoice->setValues('CedentePrestatore/Sede', array(
        'Indirizzo' => 'VIA UNIVERSO 1',
        'CAP' => '20100',
        'Comune' => 'TRENTO',
        'Provincia' => 'TN',
        'Nazione' => 'IT'
    ));
    $invoice->setValues('CessionarioCommittente', array(
        './/CodiceFiscale' => '02313821007',
        './/Denominazione' => 'AMMINISTRAZIONE',
    ));
    $invoice->setValues('CessionarioCommittente/Sede', array(
        'Indirizzo' => 'VIALE MONDO 99',
        'CAP' => '20100',
        'Comune' => 'TRENTO',
        'Provincia' => 'TN',
        'Nazione' => 'IT'
    ));
    $invoice->setValues('DatiGeneraliDocumento', array(
        'TipoDocumento' => 'TD01',
        'Divisa' => 'EUR',
        'Data' => '2018-12-12',
        'Numero' => 99999
    ));
    $invoice->setValuesAll('DatiGenerali', array(
        './/RiferimentoNumeroLinea' => 1,
        './/IdDocumento' => 4455,
        './/NumItem' => 1
    ));
    $invoice->setValues('DatiTrasporto', array(
        './/IdPaese' => 'IT',
        './/IdCodice' => '11223344556',
        './/Denominazione' => 'TRASPORTO SRLS',
        './/DataOraConsegna' => '2017-01-10T16:46:12.000+02:00'
    ));
    $invoice->setValues('DatiBeniServizi', array(
        './/NumeroLinea' => 1,
        './/Descrizione' => 'Description',
        './/Quantita' => '10.00',
        './/PrezzoUnitario' => '5.00',
        './/PrezzoTotale' => '50.00',
        './/DettaglioLinee/AliquotaIVA' => '22.00',
        './/DatiRiepilogo/AliquotaIVA' => '22.00',
        './/ImponibileImporto' => '50.00',
        './/Imposta' => '11.00',
        './/EsigibilitaIVA' => 'D'
    ));
    $invoice->setValues('DatiPagamento', array(
        './/CondizioniPagamento' => 'TP01',
        './/ModalitaPagamento' => 'MP01',
        './/DataScadenzaPagamento' => '2018-12-31',
        './/ImportoPagamento' => '61.00'
    ));

    $invoice->save(true);
}
catch (\Exception $e)
{
    echo $e->getMessage() . PHP_EOL;
}
