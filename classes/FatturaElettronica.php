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

namespace Taocomp\EinvoiceIt;

class FatturaElettronica extends AbstractDocument
{
    /**
     * Invoice formats
     */
    protected static $allowedFormats = array(
        'FPA12',
        'FPR12'
    );

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
     * Create root element "FatturaElettronica"
     */
    protected function createRootElement()
    {
        $schemaLocation = 'http://ivaservizi.agenziaentrate.gov.it/docs/xsd/fatture/v1.2 '
                        . 'http://www.fatturapa.gov.it/export/fatturazione/sdi/fatturapa'
                        . '/v1.2/Schema_del_file_xml_FatturaPA_versione_1.2.xsd';

        $root = $this->dom->createElementNS(
            'http://ivaservizi.agenziaentrate.gov.it/docs/xsd/fatture/v1.2',
            'p:FatturaElettronica');
        $root->setAttributeNS(
            'http://www.w3.org/2000/xmlns/',
            'xmlns:ds',
            'http://www.w3.org/2000/09/xmldsig#');
        $root->setAttributeNS(
            'http://www.w3.org/2000/xmlns/',
            'xmlns:xsi',
            'http://www.w3.org/2001/XMLSchema-instance');
        $root->setAttributeNS(
            'http://www.w3.org/2001/XMLSchema-instance',
            'schemaLocation',
            $schemaLocation);

        return $root;
    }

    /**
     * Return nth body element.
     * Xpath expressions allowed: "last()", "last()-1", ...
     */
    public function getBody( $i = 1 )
    {
        return $this->getElement("FatturaElettronicaBody[$i]");
    }

    /**
     * Retrieve invoice filename from current element values
     */
    public function getFilename()
    {
        $progressivoInvio = $this->getValue('ProgressivoInvio');
        $codice = $this->getValue('IdTrasmittente/IdCodice');
        $paese = $this->getValue('IdTrasmittente/IdPaese');

        if (!$progressivoInvio) {
            throw new \Exception(__FUNCTION__ . ': ProgressivoInvio is empty');
        }
        if (!$paese) {
            throw new \Exception(__FUNCTION__ . ': IdPaese is empty');
        }
        if (!$codice) {
            throw new \Exception(__FUNCTION__ . ': IdCodice is empty');
        }

        return "{$paese}{$codice}_$progressivoInvio.xml";
    }

    /**
     * Return header.
     */
    public function getHeader()
    {
        return $this->getElement("FatturaElettronicaHeader");
    }

    public function load( string $filename, int $options = 0, bool $ignoreVersion = false )
    {
        $currentVersion = $this->dom->documentElement->getAttribute('versione');
        parent::load($filename, $options);
        $newVersion = $this->dom->documentElement->getAttribute('versione');

        if (true !== $ignoreVersion && $newVersion !== $currentVersion) {
            throw new \Exception("Invoice $newVersion loaded, but a $currentVersion was created");
        }

        return $this;
    }

    /**
     * Set number of bodies (invoice lot)
     */
    public function setLotSize( int $size )
    {
        if ($size < 1) {
            throw new \Exception("Invalid lot size '$size'");
        }

        if ($size > 1) {
            $this->setElementSize('FatturaElettronicaBody', $size);
        }

        return $this;
    }

    /**
     * Set line item count
     */
    public function setLineItemCount( int $lines, int $bodyIndex = 1 )
    {
        if ($lines < 1) {
            throw new \Exception("Invalid line item count '$lines'");
        }

        if ($bodyIndex < 1) {
            throw new \Exception("Invalid body index '$bodyIndex'");
        }

        if ($lines === 1) {
            return $this;
        }

        $body = $this->getBody($bodyIndex);
        $datiRiepilogo = $this->getElement('.//DatiRiepilogo', $body);
        $this->setElementSize('.//DettaglioLinee', $lines, $body, $datiRiepilogo);

        return $this;
    }
}
