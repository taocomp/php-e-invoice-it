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
     * Xpath for header
     */
    const XPATH_HEADER = '/p:FatturaElettronica/FatturaElettronicaHeader';

    /**
     * Xpath for body
     */
    const XPATH_BODY = '/p:FatturaElettronica/FatturaElettronicaBody';

    /**
     * Invoice templates.
     */
    protected static $templates = array();

    /**
     * Optional prefix path where to save invoices.
     */
    protected static $destinationDir = null;

    /**
     * Main section in invoice header
     */
    protected static $headerSections = array(
        'DatiTrasmissione',
        'CedentePrestatore',
        'CessionarioCommittente'
    );

    /**
     * Main section in invoice body
     */
    protected static $bodySections = array(
        'DatiGenerali',
        'DatiBeniServizi',
        'DatiPagamento'
    );

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
     * Get destination dir
     */
    public static function getDestinationDir()
    {
        return self::$destinationDir;
    }

    /**
     * Shortcuts for $this->FatturaElettronica{Header,Body}->*
     */
    public function __call( string $name, $args )
    {
        if (true === in_array($name, self::$headerSections)) {
            $path = self::XPATH_HEADER;
        } else if (true === in_array($name, self::$bodySections)) {
            $path = self::XPATH_BODY;
        } else {
            throw new \Exception("Cannot find section '$name'");
        }

        $values = array();
        if (is_array($args) && false === empty($args) &&  is_array($args[0])) {
            $values = $args[0];
        }
        return $this->populateSection("$path/$name", $values);
    }

    public function header( string $path )
    {
        $pathSeparator = substr($path, 0, 1) === '/' ? '' : '/*/';
        $path = self::XPATH_HEADER . $pathSeparator . $path;
        $xpath = $this->xpath($path);
        if (1 !== count($xpath)) {
            throw new \Exception("Ambiguous path '$path'");
        }
        return $xpath[0];
    }

    /**
     * Get a main section from header or body via-xpath
     */
    protected function getSection( string $path )
    {
        $xpath = $this->xpath($path);
        
        if (false === $xpath || 1 !== count($xpath)) {
            throw new \Exception("Wrong path '$path'");
        }

        return $xpath[0];
    }

    /**
     * Populate section
     */
    protected function populateSection( string $path, array $values )
    {
        $mainNode = $this->getSection($path);

        if (false === empty($values)) {
            foreach ($values as $subpath => $value) {
                $items = explode('/', $subpath);
                if (1 === count($items)) {
                    $mainNode->$subpath = $value;
                } else {
                    $key = array_pop($items);
                    $subnode = $this->getSection("$path/" . implode('/', $items));
                    $subnode->$key = $value;
                }
            }
        }

        return $mainNode;
    }
    
    /**
     * Retrieve invoice filename
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
