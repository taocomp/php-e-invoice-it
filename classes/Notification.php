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

class Notification extends Document
{
    const EC01 = 'EC01';
    const EC02 = 'EC02';
    
    // Templates
    protected static $templates = array();

    // Client object, if any
    protected static $client = null;

    public static function factory( string $template )
    {
        $obj = parent::factory($template);

        // If $template is a notification file, don't change data
        if (is_readable($template)) {
            return $obj;
        }

        // Remove optional nodes
        if (isset($obj->Descrizione)) {
            unset($obj->Descrizione);
        }
        if (isset($obj->MessageIdCommittente)) {
            unset($obj->MessageIdCommittente);
        }
        if (isset($obj->RiferimentoFattura->PosizioneFattura)) {
            unset($obj->RiferimentoFattura->PosizioneFattura);
        }

        return $obj;
    }
    
    // --------------------------------------------------------------
    // Send notification to SdI
    // --------------------------------------------------------------
    public function send( string $filename )
    {
        $fileSdI = new FileSdI();
        $fileSdI->IdentificativoSdI = $this->IdentificativoSdI;
        $fileSdI->NomeFile = basename($filename);
        $fileSdI->File = $this->asXml();
        $fileSdI->encodeFile();
        
        return new RispostaSdINotificaEsito(self::$client->NotificaEsito($fileSdI));
    }
}
