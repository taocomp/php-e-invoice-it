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

class Notification extends AbstractDocument
{
    /**
     * Constants
     */
    const EC01 = 'EC01';
    const EC02 = 'EC02';

    /**
     * Notification templates
     */
    protected static $templates = array();

    /**
     * Notification factory
     */
    public static function factory( string $template )
    {
        $obj = parent::factory($template);

        // If $template is a notification file, don't change data
        if (is_readable($template)) {
            return $obj;
        }

        return $obj;
    }

    /**
     * Save notification
     */
    public function save( string $dest, bool $overwrite = false)
    {
        if (isset($this->Descrizione) && empty((string)$this->Descrizione)) {
            unset($this->Descrizione);
        }
        
        if (isset($this->MessageIdCommittente) && empty((string)$this->MessageIdCommittente)) {
            unset($this->MessageIdCommittente);
        }

        $RF = $this->RiferimentoFattura;
        if (isset($RF->PosizioneFattura) && empty((string)$RF->PosizioneFattura)) {
            unset($RF->PosizioneFattura);
        }

        return parent::save($dest, $overwrite);
    }
}
