<?php
use PHPUnit\Framework\TestCase;
use Taocomp\Einvoicing\FatturaElettronica;

class FatturaElettronicaTest extends TestCase
{
    /**
     * Create invoices
     ***************************************************************************
     */
    public function testCan_Create_FPA12()
    {
        $invoice = new FatturaElettronica('FPA12');

        $this->assertInstanceOf(FatturaElettronica::class, $invoice);
    }

    public function testCan_Create_FPR12()
    {
        $invoice = new FatturaElettronica('FPR12');

        $this->assertInstanceOf(FatturaElettronica::class, $invoice);
    }

    public function testCannotCreateWithInvalidFormat()
    {
        $this->expectException(\Exception::class);

        $invoice = new FatturaElettronica('BAD');
    }

    public function testCanCreateFromFile()
    {
        $invoice = new FatturaElettronica(__DIR__ . '/files/IT01234567890_FPA01.xml');

        $this->assertInstanceOf(FatturaElettronica::class, $invoice);
    }

    public function testCannotCreateFromNonExistentFile()
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

    /**
     * QUERY
     ***************************************************************************
     */
    public function testQueryTagAndNoContext()
    {
        $tag = 'ProgressivoInvio';
        $invoice = new FatturaElettronica(__DIR__ . '/files/IT01234567890_FPA01.xml');
        $list = $invoice->query($tag);

        $this->assertEquals(1, $list->count());
    }
    
    public function testQueryRelativePathAndNoContext()
    {
        $path = 'DatiGeneraliDocumento/Divisa';
        $invoice = new FatturaElettronica(__DIR__ . '/files/IT01234567890_FPA01.xml');
        $list = $invoice->query($path);

        $this->assertEquals(1, $list->count());
    }
    
    public function testQueryAbsolutePathAndNoContext()
    {
        $path = '/FatturaElettronicaBody/DatiPagamento/CondizioniPagamento';
        $invoice = new FatturaElettronica(__DIR__ . '/files/IT01234567890_FPA01.xml');
        $list = $invoice->query($path);

        $this->assertEquals(1, $list->count());
    }
    
    public function testQueryTagWithContext()
    {
        $tag = 'Imposta';
        $context = 'DatiRiepilogo';
        $invoice = new FatturaElettronica(__DIR__ . '/files/IT01234567890_FPA01.xml');
        $list = $invoice->query($tag, $context);

        $this->assertEquals(1, $list->count());
    }
    
    public function testQueryRelativePathWithContext()
    {
        $path = 'Sede/CAP';
        $context = 'CedentePrestatore';
        $invoice = new FatturaElettronica(__DIR__ . '/files/IT01234567890_FPA01.xml');
        $list = $invoice->query($path, $context);

        $this->assertEquals(1, $list->count());
    }
    
    public function testCannotQueryAbsolutePathWithContext()
    {
        $this->expectException(\Exception::class);

        $path = '/FatturaElettronicaHeader/DatiTrasmissione/CodiceDestinatario';
        $context = 'FatturaElettronicaHeader';
        $invoice = new FatturaElettronica(__DIR__ . '/files/IT01234567890_FPA01.xml');
        $list = $invoice->query($path, $context);
    }
    
    public function testQueryPathWithPredicateWithContext()
    {
        $path = './/DatiPagamento';
        $context = 'FatturaElettronicaBody[2]';
        $invoice = new FatturaElettronica(__DIR__ . '/files/IT01234567890_FPA03.xml');
        $list = $invoice->query($path, $context);

        $this->assertEquals(1, $list->count());
    }
    
    /**
     * Elements
     ***************************************************************************
     */
    public function testGetOneElementByTag()
    {
        $tag = 'ProgressivoInvio';
        $invoice = new FatturaElettronica(__DIR__ . '/files/IT01234567890_FPA01.xml');
        $element = $invoice->getElement($tag);

        $this->assertInstanceOf(\DOMNode::class, $element);
    }

    public function testAddElementNoBeforeRef()
    {
        $invoice = new FatturaElettronica('FPA12');
        $invoice->addElement('PECDestinatario', 'DatiTrasmissione');
        $invoice->setValue('PECDestinatario', 'pec@example.com');
        $value = $invoice->getValue('PECDestinatario');

        $this->assertEquals('pec@example.com', $value);
    }

    public function testAddBody()
    {
        $invoice = new FatturaElettronica('FPR12');
        $invoice->addBody(2);
        $bodies = $invoice->query('FatturaElettronicaBody');
        $count = $bodies->count();

        $this->assertEquals(3, $count);
    }

    public function testAddLineItem()
    {
        $invoice = new FatturaElettronica('FPR12');
        $invoice->addBody(2);
        $invoice->addLineItem(5, 3);
        $count = $invoice->query("/FatturaElettronicaBody[3]//DettaglioLinee")->count();
        
        $this->assertEquals(6, $count);
    }

    /**
     * Values
     ***************************************************************************
     */
    public function testGetOneValueByTag()
    {
        $tag = 'ProgressivoInvio';
        $invoice = new FatturaElettronica(__DIR__ . '/files/IT01234567890_FPA01.xml');
        $value = $invoice->getValue($tag);

        $this->assertEquals('00001', $value);
    }

    public function testSetOneValueByTagNoContext()
    {
        $tag = 'ProgressivoInvio';        
        $invoice = new FatturaElettronica('FPR12');
        $invoice->setValue($tag, '54321');
        $value = $invoice->getValue($tag);

        $this->assertEquals('54321', $value);
    }

    public function testSetValuesByTag()
    {
        $tag = 'CessionarioCommittente';        
        $invoice = new FatturaElettronica('FPR12');
        $invoice->setValues($tag, array(
            // CessionarioCommittente/DatiAnagrafici/CodiceFiscale
            'DatiAnagrafici/CodiceFiscale' => '01234567890',
            // Denominazione, somewhere inside CessionarioCommittente
            'Denominazione' => 'BETA SRL'
        ));
        $value1 = $invoice->getValue('DatiAnagrafici/CodiceFiscale', $tag);
        $value2 = $invoice->getValue('Denominazione', $tag);

        $this->assertEquals(
            array('01234567890', 'BETA SRL'),
            array($value1, $value2)
        );
    }

    public function testSetValueToAllByTagNoContext()
    {
        $tag = 'Indirizzo';        
        $invoice = new FatturaElettronica('FPR12');
        $invoice->setValueToAll($tag, 'VIA DELLE ROSE');
        $value1 = $invoice->getValue('CedentePrestatore/Sede/Indirizzo');
        $value2 = $invoice->getValue('CessionarioCommittente/Sede/Indirizzo');

        $this->assertEquals(
            array('VIA DELLE ROSE', 'VIA DELLE ROSE'),
            array($value1, $value2)
        );
    }

    public function testSetValuesToAll()
    {
        $tag = 'FatturaElettronicaBody';        
        $invoice = new FatturaElettronica('FPR12');
        $invoice->setValuesToAll($tag, array(
            'AliquotaIVA' => '22.00',
            'Denominazione' => 'TRASPORTO SPA'
        ));
        $value1 = $invoice->getValue('DettaglioLinee/AliquotaIVA', $tag);
        $value2 = $invoice->getValue('DatiRiepilogo/AliquotaIVA', $tag);
        $value3 = $invoice->getValue('Denominazione', $tag);

        $this->assertEquals(
            array('22.00', '22.00', 'TRASPORTO SPA'),
            array($value1, $value2, $value3)
        );
    }

    /**
     * Filename
     ***************************************************************************
     */
    public function testGetAValidFilename()
    {
        $invoice = new FatturaElettronica('FPR12');
        $invoice->setValue('ProgressivoInvio', '54321');
        $invoice->setValue('IdCodice', '00011122233', 'CedentePrestatore');
        $invoice->setValue('IdPaese', 'IT', 'CedentePrestatore');
        $filename = $invoice->getFilename();

        $this->assertEquals('IT00011122233_54321.xml', $filename);
    }
    
    public function testSetACustomFilename()
    {
        $prefixPath = __DIR__ . '/tmpfiles';
        $filename = 'my-custom-template.xml';
        $invoice = new FatturaElettronica('FPR12');
        $invoice->setValue('IdTrasmittente/IdCodice', '00011122233');
        $invoice->setValue('IdTrasmittente/IdPaese', 'IT');
        $invoice->setFilename($filename);
        $invoice->setPrefixPath($prefixPath)->save(true);

        $this->assertFileIsReadable($prefixPath . "/$filename");
    }
}
