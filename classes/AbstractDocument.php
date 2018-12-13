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

abstract class AbstractDocument extends \SimpleXMLElement
{
    /**
     * Set an XML template from file (FPA12, FPR12, EC, ...)
     */
    public static function setTemplate( string $version, string $file )
    {
        if (false === is_readable($file)) {
            throw new \Exception("File '$file' not found or not readable");
        }

        static::$templates[$version] = file_get_contents($file);
    }

    public static function getTemplate( string $template )
    {
        if (false === array_key_exists($template, static::$templates)) {
            throw new \Exception("Cannot find a template for '$template'");
        }

        return static::$templates[$template];
    }

    /**
     * Invoice/Notification factory.
     *
     * You can pass a filepath or a template key as param:
     * Invoice::factory('/path/to/invoice.xml');
     * Invoice::factory('FPR12');
     */
    public static function factory( string $template )
    {
        if (is_readable($template)) {
            // We pass a file
            return new static($template, 0, true);
        } else {
            // We pass a template key
            $template = static::getTemplate($template);
            return new static($template);
        }
        
        throw new \Exception("Cannot create a document from '$template'");
    }

    /**
     * Save document to file.
     */
    public function save( string $dest, bool $overwrite = false )
    {
        $classArray = explode('\\', __CLASS__);
        $class = array_pop($classArray);

        if (file_exists($dest) && false === $overwrite) {
            throw new \Exception("$class '$dest' already exists");
        }

        $xml = $this->asXml();

        // Remove empty lines, if any
        $xml = preg_replace("/(\R){2,}/", "$1", $xml);
        
        if (false === file_put_contents($dest, $xml, LOCK_EX)) {
            throw new \Exception("Cannot save $class to '$dest'");
        }

        return $this;
    }
}
