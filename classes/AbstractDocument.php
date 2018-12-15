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

abstract class AbstractDocument
{
    /**
     * Optional prefix path where to save invoices.
     */
    protected static $destinationDir = null;

    /**
     * \DOMDocument object
     */
    protected $dom = null;

    /**
     * Get destination dir
     */
    public static function getDestinationDir()
    {
        return static::$destinationDir;
    }
    
    /**
     * Set destination dir (common prefix path when saving invoices)
     */
    public static function setDestinationDir( string $dir )
    {
        if (false === is_writeable($dir)) {
            throw new \Exception("Directory '$dir' is not writeable");
        }

        static::$destinationDir = $dir;
    }

    /**
     * Constructor
     */
    public function __construct( $file = null )
    {
        $this->dom = new \DOMDocument('1.0', 'UTF-8');
        $this->dom->formatOutput = true;

        if (null === $file) {
            $root = $this->createRootElement();

            if (false === is_a($root, '\DOMElement')) {
                throw new \Exception('Method createRootElement must return a \DOMElement');
            }

            if (false === is_array(static::$templateArray)) {
                throw new \Exception('static templateArray must be an array');
            }

            $this->createElementsFromArray($root, static::$templateArray);
            $this->dom->appendChild($root);
        } else if (is_readable($file)) {
            $this->load($file);
        } else {
            throw new \Exception("File '$file' not found or not readable");
        }
    }

    /**
     * Return XML as string
     */
    public function asXML( bool $normalize = false )
    {
        if (true === $normalize) {
            $this->normalize();
        }

        return $this->dom->saveXML(null, LIBXML_NOEMPTYTAG);
    }

    /**
     * Recursively adds elements from array
     */
    protected function createElementsFromArray( \DOMElement $parent, array $array )
    {
        foreach ($array as $k => $v) {
            if (true === is_array($v)) {
                $node = $this->dom->createElement($k);
                $this->createElementsFromArray($node, $v);
            } else {
                $node = $this->dom->createElement($k, $v);
            }
            $parent->appendChild($node);
        }
    }

    /**
     * Create and return the root element
     */
    abstract protected function createRootElement();
    
    /**
     * Get class name
     */
    protected function getClassName()
    {
        $classArray = explode('\\', get_class($this));
        return array_pop($classArray);
    }

    /**
     * Returns \DOMDocument object
     */
    public function getDOM()
    {
        return $this->dom;
    }

    /**
     * Get an element by xpath, if the xpath returns only one element.
     * The xpath can be relative to a $contextNode.
     *
     * Throws an exception if result element count != 1.
     */
    public function getElement( string $xpath, \DOMNode $contextNode = null )
    {
        $elements = $this->getElements($xpath, $contextNode);
        $count = $elements->count();

        if ($count !== 1) {
            $msg = null !== $contextNode
                 ? "Element '$xpath' (relative to {$contextNode->nodeName})"
                 : "Element '$xpath'";

            if ($count > 1) {
                throw new \Exception("$msg is not unique");
            } else if ($count === 0) {
                throw new \Exception("$msg not found");
            }
        }

        return $elements[0];
    }

    /**
     * Get an element by xpath.
     * The xpath can be relative to a $contextNode.
     */
    public function getElements( string $xpath, \DOMNode $contextNode = null )
    {
        $strpos = strpos($xpath, '/');
        
        if ((false === $strpos || $strpos > 1) && null === $contextNode) {
            $xpath = "//$xpath";
        }

        return $this->query($xpath, $contextNode);
    }

    /**
     * Get filename
     */
    abstract public function getFilename();

    /**
     * Get value
     */
    public function getValue( string $xpath, \DOMNode $contextNode = null )
    {
        return $this->getElement($xpath, $contextNode)->nodeValue;
    }

    /**
     * Load a document from file
     */
    public function load( string $filename, int $options = 0 )
    {
        if (false === $this->dom->load($filename, $options)) {
            throw new \Exception("Cannot load file '$filename'");
        }
    }

    /**
     * Remove empty elements.
     */
    public function normalize()
    {
        foreach( $this->query('//*[not(node())]') as $node ) {
            $node->parentNode->removeChild($node);
        }

        return $this;
    }

    /**
     * Query via-xpath.
     * Returns a \DOMNodeList.
     */
    public function query( string $expr, \DOMNode $contextNode = null, bool $registerNodeNS = true )
    {
        $xpath = new \DOMXpath($this->dom);
        return $xpath->query($expr, $contextNode, $registerNodeNS);
    }

    /**
     * Save document to file.
     * If set, prepend static::$destinationDir to path.
     * Overwrite existing file if $overwrite is true.
     */
    public function save( bool $overwrite = false, bool $normalize = true )
    {
        $dest = $this->getFilename();

        if (false === is_string($dest) || empty($dest)) {
            throw new \Exception("Filename '$dest' empty or invalid");
        }
        
        // Prepend destination dir if $dest is not absolute and
        // static::$destinationDir is valid.
        if (0 !== strpos($dest, '/') && is_readable(static::$destinationDir)) {
            $dest = static::$destinationDir . "/$dest";
        }

        $className = $this->getClassName();

        if (file_exists($dest) && false === $overwrite) {
            throw new \Exception("$className '$dest' already exists");
        }

        if (true === $normalize) {
            $this->normalize();
        }

        if (false === $this->dom->save($dest, LIBXML_NOEMPTYTAG)) {
            throw new \Exception("Cannot save $className to '$dest'");
        }

        return $this;
    }

    /**
     * Change size of a specified element
     */
    public function setElementSize( string $xpath, int $size, \DOMNode $contextNode = null, \DOMNode $keepLast = null )
    {
        $element = $this->getElement($xpath, $contextNode);
        
        for ($i = 2; $i <= $size; $i++) {
            $cloned = $element->cloneNode(true);

            if (null === $keepLast) {
                $element->parentNode->appendChild($cloned);
            } else {
                $element->parentNode->insertBefore($cloned, $keepLast);
            }
        }

        return $this;
    }

    /**
     * Set value for a given element, if the element is unique (by tag or xpath).
     * Throws an exception otherwise.
     */
    public function setValue( string $expr, $value, \DOMNode $contextNode = null )
    {
        $this->getElement($expr, $contextNode)->nodeValue = $value;

        return $this;
    }

    /**
     * Set same value $value to all elements retrieved through $expr
     */
    public function setValueAll( string $expr, $value, \DOMNode $contextNode = null )
    {
        $elements = $this->getElements($expr, $contextNode);

        foreach ($elements as $element) {
            $element->nodeValue = $value;
        }

        return $this;
    }

    /**
     * Set values from an associative array. Keys must return just one element.
     * Array keys are relative to node $context (or retrieved through context expression).
     */
    public function setValues( $context, array $array )
    {
        if (is_string($context)) {
            $context = $this->getElement($context);
        } else if (false === is_a($context, \DOMNode)) {
            throw new \Exception('Invalid context');
        }
        
        foreach ($array as $k => $v) {
            $this->setValue($k, $v, $context);
        }

        return $this;
    }

    /**
     * Set values from an associative array. Keys may return N elements.
     * Array keys are relative to node $context (or retrieved through context expression).
     */
    public function setValuesAll( $context, array $array )
    {
        if (is_string($context)) {
            $context = $this->getElement($context);
        } else if (false === is_a($context, \DOMNode)) {
            throw new \Exception('Invalid context');
        }
        
        foreach ($array as $k => $v) {
            $this->setValueAll($k, $v, $context);
        }

        return $this;
    }
}
