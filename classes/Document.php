<?php namespace Taocomp\Sdicoop;

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
