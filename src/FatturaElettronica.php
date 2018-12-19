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

namespace Taocomp\Einvoicing;

class FatturaElettronica extends AbstractDocument
{
    /**
     * Constants for root element ("FatturaElettronica")
     */
    const ROOT_TAG_PREFIX = 'p';
    const ROOT_TAG_NAME   = 'FatturaElettronica';
    const ROOT_NAMESPACE  = 'http://ivaservizi.agenziaentrate.gov.it/docs/xsd/fatture/v1.2';
    const SCHEMA_LOCATION = 'http://ivaservizi.agenziaentrate.gov.it/docs/xsd/fatture/v1.2 '
                          . 'http://www.fatturapa.gov.it/export/fatturazione/sdi/fatturapa'
                          . '/v1.2/Schema_del_file_xml_FatturaPA_versione_1.2.xsd';

    /**
     * Invoice formats
     */
    protected static $allowedFormats = array('FPA12', 'FPR12');

    /**
     * Default destination dir where to save documents
     */
    protected static $defaultPrefixPath = null;

    /**
     * Invoice elements
     */
    public static $templateArray = array(
        'FatturaElettronicaHeader' => array(
            'DatiTrasmissione' => array(
                'IdTrasmittente' => array(
                    'IdPaese' => '',
                    'IdCodice' => ''
                ),
                'ProgressivoInvio' => '',
                'FormatoTrasmissione' => '',
                'CodiceDestinatario' => '',
            ),
            'CedentePrestatore' => array(
                'DatiAnagrafici' => array(
                    'IdFiscaleIVA' => array(
                        'IdPaese' => '',
                        'IdCodice' => ''
                    ),
                    'Anagrafica' => array(
                        'Denominazione' => ''
                    ),
                    'RegimeFiscale' => '',
                ),
                'Sede' => array(
                    'Indirizzo' => '',
                    'CAP' => '',
                    'Comune' => '',
                    'Provincia' => '',
                    'Nazione' => ''
                )
            ),
            'CessionarioCommittente' => array(
                'DatiAnagrafici' => array(
                    'CodiceFiscale' => '',
                    'Anagrafica' => array(
                        'Denominazione' => ''
                    )
                ),
                'Sede' => array(
                    'Indirizzo' => '',
                    'CAP' => '',
                    'Comune' => '',
                    'Provincia' => '',
                    'Nazione' => ''
                )
            )
        ),
        'FatturaElettronicaBody' => array(
            'DatiGenerali' => array(
                'DatiGeneraliDocumento' => array(
                    'TipoDocumento' => '',
                    'Divisa' => '',
                    'Data' => '',
                    'Numero' => '',
                    'Causale' => ''
                ),
                'DatiOrdineAcquisto' => array(
                    'RiferimentoNumeroLinea' => '',
                    'IdDocumento' => '',
                    'NumItem' => '',
                    'CodiceCUP' => '',
                    'CodiceCIG' => ''
                ),
                'DatiContratto' => array(
                    'RiferimentoNumeroLinea' => '',
                    'IdDocumento' => '',
                    'NumItem' => '',
                    'CodiceCUP' => '',
                    'CodiceCIG' => ''
                ),
                'DatiConvenzione' => array(
                    'RiferimentoNumeroLinea' => '',
                    'IdDocumento' => '',
                    'NumItem' => '',
                    'CodiceCUP' => '',
                    'CodiceCIG' => ''
                ),
                'DatiRicezione' => array(
                    'RiferimentoNumeroLinea' => '',
                    'IdDocumento' => '',
                    'NumItem' => '',
                    'CodiceCUP' => '',
                    'CodiceCIG' => ''
                ),
                'DatiTrasporto' => array(
                    'DatiAnagraficiVettore' => array(
                        'IdFiscaleIVA' => array(
                            'IdPaese' => '',
                            'IdCodice' => ''
                        ),
                        'Anagrafica' => array(
                            'Denominazione' => ''
                        )
                    ),
                    'DataOraConsegna' => '',
                )
            ),
            'DatiBeniServizi' => array(
                'DettaglioLinee' => array(
                    'NumeroLinea' => '',
                    'Descrizione' => '',
                    'Quantita' => '',
                    'PrezzoUnitario' => '',
                    'PrezzoTotale' => '',
                    'AliquotaIVA' => ''
                ),
                'DatiRiepilogo' => array(
                    'AliquotaIVA' => '',
                    'ImponibileImporto' => '',
                    'Imposta' => '',
                    'EsigibilitaIVA' => ''
                )
            ),
            'DatiPagamento' => array(
                'CondizioniPagamento' => '',
                'DettaglioPagamento' => array(
                    'ModalitaPagamento' => '',
                    'DataScadenzaPagamento' => '',
                    'ImportoPagamento' => ''
                )
            ),
        )
    );

    /**
     * Constructor
     */
    public function __construct( string $arg )
    {
        $format = strtoupper($arg);

        if (true === in_array($format, self::$allowedFormats)) {
            // New invoice
            parent::__construct();
            $this->dom->documentElement->setAttribute('versione', $format);
            $this->setValue('FormatoTrasmissione', $format);
        } else {
            // Load invoice from file
            parent::__construct($arg);
        }
    }

    /**
     * Retrieve invoice filename from current element values
     * if $this->filename is not set
     */
    public function getFilename()
    {
        if (null !== $this->filename) {
            return $this->filename;
        }

        $progressivoInvio = $this->getValue('ProgressivoInvio');
        $codice = $this->getValue('IdCodice', 'CedentePrestatore');
        $paese = $this->getValue('IdPaese', 'CedentePrestatore');

        if (empty($progressivoInvio)) {
            throw new \Exception(__FUNCTION__ . ': ProgressivoInvio is empty');
        }
        if (empty($paese)) {
            throw new \Exception(__FUNCTION__ . ': IdPaese is empty');
        }
        if (empty($codice)) {
            throw new \Exception(__FUNCTION__ . ': IdCodice is empty');
        }

        return "{$paese}{$codice}_$progressivoInvio.xml";
    }

    /**
     * Add body (invoice lot)
     */
    public function addBody( int $n = 1 )
    {
        if ($n < 1) {
            return $this;
        }

        $body = $this->getBody();
        
        for ($i = 0; $i < $n; $i++) {
            $this->addElement($body->cloneNode(true), '/');
        }

        return $this;
    }

    /**
     * Get body
     */
    public function getBody( int $bodyIndex = 1 )
    {
        return $this->getElement("FatturaElettronicaBody[$bodyIndex]");
    }

    /**
     * Add line item
     */
    public function addLineItem( int $n, int $bodyIndex = 1 )
    {
        if ($n < 1) {
            return $this;
        }

        if ($bodyIndex < 1) {
            throw new \Exception("Invalid body index '$bodyIndex'");
        }

        $body = $this->getBody($bodyIndex);
        $line = $this->getElement('DettaglioLinee', $body);
        $parent = $this->getElement('DatiBeniServizi', $body);
        $beforeRef = $this->getElement('DatiRiepilogo', $body);

        for ($i = 0; $i < $n; $i++) {
            $this->addElement($line->cloneNode(true), $parent, $beforeRef);
        }

        return $this;
    }
}
