<?php
use PHPUnit\Framework\TestCase;
use Taocomp\Einvoicing\FatturaElettronica;

class FatturaElettronicaTest extends TestCase
{
    /**
     * Create invoices
     ***************************************************************************
     */
    public function testCreate_FPA12()
    {
        $invoice = new FatturaElettronica('FPA12');

        $this->assertInstanceOf(FatturaElettronica::class, $invoice);
    }

    public function testCreate_FPR12()
    {
        $invoice = new FatturaElettronica('FPR12');

        $this->assertInstanceOf(FatturaElettronica::class, $invoice);
    }

    public function testCreateWithInvalidFormat()
    {
        $this->expectException(\Exception::class);

        $invoice = new FatturaElettronica('BAD');
    }

    public function testCreateFromFile()
    {
        $invoice = new FatturaElettronica(__DIR__ . '/files/IT01234567890_FPA01.xml');

        $this->assertInstanceOf(FatturaElettronica::class, $invoice);
    }

    public function testCreateFromNonExistentFile()
    {
        $this->expectException(\Exception::class);

        $invoice = new FatturaElettronica(__DIR__ . '/files/this-file-does-not-exist.xml');
    }

    /**
     * DOM object
     ***************************************************************************
     */
    public function testDomIsAValidObject()
    {
        $invoice = new FatturaElettronica('FPA12');

        $this->assertInstanceOf(\DOMDocument::class, $invoice->getDOM());
    }
}
