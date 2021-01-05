<?php

namespace SlamFatturaElettronica;

use DOMDocument;

final class Validator implements ValidatorInterface
{
    const XSD_FATTURA_ORDINARIA_1_2         = 'Schema_VFPR12.xsd';
    const XSD_FATTURA_ORDINARIA_1_2_1       = 'Schema_VFPR121a.xsd';
    const XSD_FATTURA_ORDINARIA_LATEST      = 'Schema_VFPR121a.xsd';

    const XSD_FATTURA_SEMPLIFICATA_1_0      = 'Schema_VFSM10.xsd';
    const XSD_FATTURA_SEMPLIFICATA_LATEST   = 'Schema_VFSM10.xsd';

    const XSD_MESSAGGI_1_0                  = 'MessaggiFatturaTypes_v1.0.xsd';
    const XSD_MESSAGGI_1_1                  = 'MessaggiTypes_v1.1.xsd';
    const XSD_MESSAGGI_LATEST               = 'MessaggiTypes_v1.1.xsd';

    private $xsdCache = array();

    /**
     * @throws Exception\InvalidXmlStructureException
     * @throws Exception\InvalidXsdStructureComplianceException
     */
    public function assertValidXml($xml, $type = self::XSD_FATTURA_ORDINARIA_LATEST)
    {
        $dom = new DOMDocument();
        $dom->recover = true;
        $dom->loadXML($xml, \LIBXML_NOERROR);
        $xsd = $this->getXsd($type);

        $xsdErrorArguments = null;
        \set_error_handler(function ($errno, $errstr = '', $errfile = '', $errline = 0) use (& $xsdErrorArguments) {
            $xsdErrorArguments = func_get_args();
        });
        $dom->schemaValidateSource($xsd);
        \restore_error_handler();

        if (null === $xsdErrorArguments) {
            return;
        }

        $dom = new DOMDocument();
        $xmlErrorArguments = null;
        \set_error_handler(function ($errno, $errstr = '', $errfile = '', $errline = 0) use (& $xmlErrorArguments) {
            $xmlErrorArguments = func_get_args();
        });
        $dom->loadXML($xml);
        \restore_error_handler();

        if (null !== $xmlErrorArguments) {
            throw new Exception\InvalidXmlStructureException($xmlErrorArguments[1], $xmlErrorArguments[0], $xmlErrorArguments[0], $xmlErrorArguments[2], $xmlErrorArguments[3]);
        }

        throw new Exception\InvalidXsdStructureComplianceException($xsdErrorArguments[1], $xsdErrorArguments[0], $xsdErrorArguments[0], $xsdErrorArguments[2], $xsdErrorArguments[3]);
    }

    private function getXsd($type)
    {
        if (! isset($this->xsdCache[$type])) {
            $xsdFilename = \dirname(__DIR__) . '/xsd/' . $type;

            /** @var string $xsd */
            $xsd = \file_get_contents($xsdFilename);

            // Let's get rid of external HTTP call
            $xmldsigFilename       = \dirname(__DIR__) . '/xsd/xmldsig-core-schema.xsd';
            $this->xsdCache[$type] = \preg_replace('/(\bschemaLocation=")[^"]+"/', \sprintf('\1%s"', $xmldsigFilename), $xsd);
        }

        return $this->xsdCache[$type];
    }
}
