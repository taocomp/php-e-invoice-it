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

namespace Taocomp\EinvoiceIt;

abstract class AbstractNotification extends AbstractDocument
{
    /**
     * Filename
     */
    protected $filename = null;

    /**
     * Create root element
     */
    protected function createRootElement()
    {
        $fragment = $this->dom->createDocumentFragment();
        $fragment->appendXML('<?xml-stylesheet type="text/xsl" href="EC_v1.0.xsl"?>');
        $this->dom->appendChild($fragment);

        $type = $this->getClassName();

        $root = $this->dom->createElementNS(
            'http://www.fatturapa.gov.it/sdi/messaggi/v1.0',
            "types:$type");
        $root->setAttributeNS(
            'http://www.w3.org/2000/xmlns/',
            'xmlns:ds',
            'http://www.w3.org/2000/09/xmldsig#');
        $root->setAttributeNS(
            'http://www.w3.org/2000/xmlns/',
            'xmlns:xsi',
            'http://www.w3.org/2001/XMLSchema-instance');
        $root->setAttribute('versione', '1.0');
        $root->setAttributeNS(
            'http://www.w3.org/2001/XMLSchema-instance',
            'schemaLocation',
            'http://www.fatturapa.gov.it/sdi/messaggi/v1.0 MessaggiTypes_v1.0.xsd ');

        return $root;
    }

    /**
     * Get filename
     */
    public function getFilename()
    {
        return $this->filename;
    }
    
    /**
     * Set filename
     */
    public function setFilename( string $filename )
    {
        $this->filename = $filename;
        return $this;
    }

    public function setFilenameFromInvoice( FatturaElettronica $invoice, string $suffix )
    {
        $filename = basename($invoice->getFilename(), '.xml') . $suffix . '.xml';
        return $this->setFilename($filename);
    }

    /**
     * Populate some notification values from invoice
     */
    abstract public function setValuesFromInvoice( FatturaElettronica $invoice, $body = 1 );
}
