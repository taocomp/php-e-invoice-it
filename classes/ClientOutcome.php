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

class ClientOutcome extends AbstractNotice
{
    /**
     * Notice type
     */
    const TYPE = 'NotificaEsitoCommittente';
    
    /**
     * Constants for "Esito"
     */
    const EC01 = 'EC01';
    const EC02 = 'EC02';

    /**
     * Notice elements
     */
    public static $templateArray = array(
        'IdentificativoSdI' => '',
        'RiferimentoFattura' => array(
            'NumeroFattura' => '',
            'AnnoFattura' => ''
        ),
        'Esito' => ''
    );

    /**
     * Populate notice values from invoice
     */
    public function setValuesFromInvoice( Invoice $invoice, $body = 1 )
    {
        $body = $invoice->getBody($body);
        $this->setValue('NumeroFattura', $invoice->getValue(".//DatiGeneraliDocumento/Numero", $body));

        $anno = substr($invoice->getValue('.//DatiGeneraliDocumento/Data', $body), 0, 4);
        $this->setValue('AnnoFattura', $anno);

        return $this;
    }
}
