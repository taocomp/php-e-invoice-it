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

use \Taocomp\Sdicoop\Notification;

try
{
    require_once(__DIR__ . '/../autoload.php');

    // Add a notification template
    Notification::setTemplate('EC', __DIR__ . '/../templates/EC.xml');

    $notification = Notification::factory('EC');

    // Edit data
    $notification->IdentificativoSdI = 1010101;
    $notification->RiferimentoFattura->NumeroFattura = 44525;
    $notification->RiferimentoFattura->AnnoFattura = 2018;
    $notification->Esito = Notification::EC02;

    // Save to file
    $notification->save('my-invoice_EC_002.xml', true);
}
catch (\Exception $e)
{
    echo $e->getMessage() . PHP_EOL;
}
