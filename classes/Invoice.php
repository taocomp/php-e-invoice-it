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

namespace Taocomp\Sdicoop;

class Invoice extends AbstractDocument
{
    /**
     * Invoice templates
     */
    protected static $templates = array();

    /**
     * Optional prefix path where to save invoices.
     */
    protected static $destinationDir = null;

    /**
     * Invoice factory
     */
    public static function factory( string $template )
    {
        $obj = parent::factory($template);

        // If $template is an invoice file, don't change data
        if (is_readable($template)) {
            return $obj;
        }

        // Set invoice format
        $obj->FatturaElettronicaHeader->DatiTrasmissione->FormatoTrasmissione = $template;

        return $obj;
    }

    /**
     * Set destination dir (common prefix path when saving invoices)
     */
    public static function setDestinationDir( string $dir )
    {
        if (false === is_writeable($dir)) {
            throw new \Exception("Directory '$dir' is not writeable");
        }

        self::$destinationDir = $dir;
    }

    /**
     * Set company data
     */
    public function setCompanyData( array $data )
    {
        $IT = $this->FatturaElettronicaHeader->DatiTrasmissione->IdTrasmittente;
        $IT->IdCodice = $data['IdCodice'];
        $IT->IdPaese  = $data['IdPaese'];

        $DA = $this->FatturaElettronicaHeader->CedentePrestatore->DatiAnagrafici;
        $DA->Anagrafica->Denominazione = $data['Denominazione'];
        $DA->IdFiscaleIVA->IdPaese     = $data['IdPaese'];
        $DA->IdFiscaleIVA->IdCodice    = $data['IdCodice'];
        $DA->RegimeFiscale             = $data['RegimeFiscale'];

        $Sede = $this->FatturaElettronicaHeader->CedentePrestatore->Sede;
        $Sede->Indirizzo = $data['Indirizzo'];
        $Sede->CAP       = $data['CAP'];
        $Sede->Comune    = $data['Comune'];
        $Sede->Provincia = $data['Provincia'];
        $Sede->Nazione   = $data['Nazione'];

        return $this;
    }

    /**
     * Set value for "PECDestinatario" (and create node if not present)
     */
    public function setPECDestinatario( string $value )
    {
        $DT = $this->FatturaElettronicaHeader->DatiTrasmissione;
        
        if (!isset($DT->PECDestinatario)) {
            $DT->addChild('PECDestinatario');
        }
        
        $DT->PECDestinatario = $value;

        return $this;
    }

    /**
     * Unset node "PECDestinatario"
     */
    public function unsetPECDestinatario()
    {
        $DT = $this->FatturaElettronicaHeader->DatiTrasmissione;

        if (isset($DT->PECDestinatario)) {
            unset($DT->PECDestinatario);
        }

        return $this;
    }

    /**
     * Get a valid invoice filename
     */
    public function getFilename()
    {
        $id = (string)$this->FatturaElettronicaHeader->DatiTrasmissione->ProgressivoInvio;
        $IT = $this->FatturaElettronicaHeader->DatiTrasmissione->IdTrasmittente;
        $codice = (string)$IT->IdCodice;
        $paese = (string)$IT->IdPaese;

        if (!$id) {
            throw new \Exception(__FUNCTION__ . ': ProgressivoInvio is empty');
        }
        if (!$paese) {
            throw new \Exception(__FUNCTION__ . ': IdPaese is empty');
        }
        if (!$codice) {
            throw new \Exception(__FUNCTION__ . ': IdCodice is empty');
        }

        return "{$paese}{$codice}_$id.xml";
    }

    /**
     * Save invoice to $destDir or self::$destinationDir or current dir.
     */
    public function save( string $destDir = null, bool $overwrite = false )
    {
        $filename = $this->getFilename();

        if (is_readable($destDir)) {
            return parent::save("$destDir/$filename", $overwrite);
        }

        if (is_readable(self::$destinationDir)) {
            return parent::save(self::$destinationDir . "/$filename", $overwrite);
        }

        return parent::save($filename, $overwrite);
    }

    /**
     * Create a new notification for/from current invoice
     */
    public function prepareNotification( $template )
    {
        $DGD = $this->FatturaElettronicaBody->DatiGenerali->DatiGeneraliDocumento;
        $NumeroFattura = (string)$DGD->Numero;
        $AnnoFattura = substr((string)$DGD->Data, 0, 4);

        $notification = Notification::factory($template);
        $notification->RiferimentoFattura->NumeroFattura = $NumeroFattura;
        $notification->RiferimentoFattura->AnnoFattura = $AnnoFattura;
        $notification->Esito = Notification::EC01;

        return $notification;
    }
}
