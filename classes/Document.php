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

class Document extends \SimpleXMLElement
{
    // --------------------------------------------------------------
    // Add templates (FPR12, FPA12, EC, ...)
    // --------------------------------------------------------------    
    public static function addTemplate( string $version, string $file )
    {
        if (false === is_readable($file)) {
            throw new \Exception("File '$file' not found or not readable");
        }

        $version = strtoupper($version);
        static::$templates[$version] = file_get_contents($file);
    }

    // --------------------------------------------------------------
    // Set/Get Client
    // --------------------------------------------------------------
    public static function setClient( Client $client )
    {
        static::$client = $client;
    }
    public static function getClient()
    {
        return static::$client;
    }

    // --------------------------------------------------------------
    // Factory
    // --------------------------------------------------------------
    public static function factory( string $template )
    {
        if (is_readable($template)) {
            return new static($template, 0, true);
        } else {
            $template = strtoupper($template);

            if (!array_key_exists($template, static::$templates)) {
                throw new \Exception("Cannot find template for '$template'");
            }

            return new static(static::$templates[$template]);
        }
        
        throw new \Exception("Cannot create a document from '$template'");
    }

    // --------------------------------------------------------------
    // Save document to file
    // --------------------------------------------------------------
    public function save( string $file, bool $overwrite = false )
    {
        $classArray = explode('\\', __CLASS__);
        $class = array_pop($classArray);

        if (file_exists($file) && false === $overwrite) {
            throw new \Exception("$class '$file' already exists");
        }

        if (false === file_put_contents($file, $this->asXml(), LOCK_EX)) {
            throw new \Exception("Cannot save $class to '$file'");
        }

        return $this;
    }
}
