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

abstract class AbstractNotice extends AbstractDocument
{
    /**
     * Constants for root notice element
     */
    const ROOT_TAG_PREFIX = 'types';
    const ROOT_NAMESPACE  = 'http://www.fatturapa.gov.it/sdi/messaggi/v1.0';
    const SCHEMA_LOCATION = 'http://www.fatturapa.gov.it/sdi/messaggi/v1.0 MessaggiTypes_v1.0.xsd ';

    /**
     * Default destination dir where to save documents
     */
    protected static $defaultPrefixPath = null;

    /**
     * Constructor
     */
    public function __construct( $file = null )
    {
        parent::__construct($file);

        if (null === $file) {
            $this->dom->documentElement->setAttribute('versione', '1.0');
        }
    }

    public function setFilenameFromInvoice( FatturaElettronica $invoice, string $suffix )
    {
        $filename = basename($invoice->getFilename(), '.xml') . $suffix . '.xml';
        return $this->setFilename($filename);
    }

    /**
     * Populate some notice values from invoice
     */
    abstract public function setValuesFromInvoice( FatturaElettronica $invoice, $body = 1 );
}
