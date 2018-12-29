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

        $this->assertEquals(1, $list->length);
    }
    
    public function testQueryRelativePathAndNoContext()
    {
        $path = 'DatiGeneraliDocumento/Divisa';
        $invoice = new FatturaElettronica(__DIR__ . '/files/IT01234567890_FPA01.xml');
        $list = $invoice->query($path);

        $this->assertEquals(1, $list->length);
    }
    
    public function testQueryAbsolutePathAndNoContext()
    {
        $path = '/FatturaElettronicaBody/DatiPagamento/CondizioniPagamento';
        $invoice = new FatturaElettronica(__DIR__ . '/files/IT01234567890_FPA01.xml');
        $list = $invoice->query($path);

        $this->assertEquals(1, $list->length);
    }
    
    public function testQueryTagWithContext()
    {
        $tag = 'Imposta';
        $context = 'DatiRiepilogo';
        $invoice = new FatturaElettronica(__DIR__ . '/files/IT01234567890_FPA01.xml');
        $list = $invoice->query($tag, $context);

        $this->assertEquals(1, $list->length);
    }
    
    public function testQueryRelativePathWithContext()
    {
        $path = 'Sede/CAP';
        $context = 'CedentePrestatore';
        $invoice = new FatturaElettronica(__DIR__ . '/files/IT01234567890_FPA01.xml');
        $list = $invoice->query($path, $context);

        $this->assertEquals(1, $list->length);
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

        $this->assertEquals(1, $list->length);
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
        $invoice->addElement('PECDestinatarioExtra', 'DatiTrasmissione');
        $invoice->setValue('PECDestinatarioExtra', 'pec@example.com');
        $value = $invoice->getValue('PECDestinatarioExtra');

        $this->assertEquals('pec@example.com', $value);
    }

    public function testAddBody()
    {
        $invoice = new FatturaElettronica('FPR12');
        $invoice->addBody(2);
        $bodies = $invoice->query('FatturaElettronicaBody');
        $count = $bodies->length;

        $this->assertEquals(3, $count);
    }

    public function testAddLineItem()
    {
        $invoice = new FatturaElettronica('FPR12');
        $invoice->addBody(2);
        $invoice->addLineItem(5, 3);
        $count = $invoice->query("/FatturaElettronicaBody[3]//DettaglioLinee")->length;
        
        $this->assertEquals(6, $count);
    }

    public function testSplitElement()
    {
        $string = 'Lorem ipsum dolor sit amet, consectetur adipisci elit, sed eiusmod tempor incidunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur. Quis aute iure reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint obcaecat cupiditat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.';
        $invoice = new FatturaElettronica('FPR12');
        $invoice->setValue('DatiGeneraliDocumento/Causale', $string);

        // 2 times, it should be idempotent
        $invoice->normalize();
        $invoice->normalize();

        $causaleCount = $invoice->query('DatiGeneraliDocumento/Causale')->length;
        $lastChunk = 'lpa qui officia deserunt mollit anim id est laborum.';

        $this->assertEquals('3 lpa qui officia deserunt mollit anim id est laborum.', "$causaleCount $lastChunk");        
    }

    public function testSetElementCount()
    {
        $invoice = new FatturaElettronica('FPR12');
        $invoice->setElementCount('ProgressivoInvio', 4);
        $invoice->setElementCount('ProgressivoInvio', 3);
        $count = $invoice->query('ProgressivoInvio')->length;

        $this->assertEquals(3, $count);
    }

    /**
     * Values
     ***************************************************************************
     */
    public function testGetValueByTag()
    {
        $tag = 'ProgressivoInvio';
        $invoice = new FatturaElettronica(__DIR__ . '/files/IT01234567890_FPA01.xml');
        $value = $invoice->getValue($tag);

        $this->assertEquals('00001', $value);
    }

    public function testGetValueWithContext()
    {
        $invoice = new FatturaElettronica(__DIR__ . '/files/IT01234567890_FPA02.xml');
        $value = $invoice->getValue('NumItem', 'DatiContratto');

        $this->assertEquals('5', (string)$value);
    }

    public function testGetValueByAbsolutePath()
    {
        $invoice = new FatturaElettronica(__DIR__ . '/files/IT01234567890_FPA02.xml');
        $value = $invoice->getValue('/FatturaElettronicaHeader/DatiTrasmissione/CodiceDestinatario');

        $this->assertEquals('AAAAAA', $value);
    }

    public function testGetValueByRelativePath()
    {
        $invoice = new FatturaElettronica(__DIR__ . '/files/IT01234567890_FPA02.xml');
        $value = $invoice->getValue('DettaglioLinee[2]/NumeroLinea');

        $this->assertEquals('2', (string)$value);
    }

    public function testCannotGetAmbiguousValue()
    {
        $this->expectException(\Exception::class);

        $invoice = new FatturaElettronica(__DIR__ . '/files/IT01234567890_FPA02.xml');
        $value = $invoice->getValue('IdPaese');
    }

    public function testCannotGetAmbiguousValue2()
    {
        $this->expectException(\Exception::class);

        $invoice = new FatturaElettronica(__DIR__ . '/files/IT01234567890_FPA02.xml');
        $value = $invoice->getValue('Sede/Indirizzo', 'FatturaElettronicaHeader');
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
            'Anagrafica/Denominazione' => 'BETA SRL'
        ));
        $value1 = $invoice->getValue('DatiAnagrafici/CodiceFiscale', $tag);
        $value2 = $invoice->getValue('Anagrafica/Denominazione', $tag);

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

    public function testSetValuesToLineItem()
    {
        $invoice = new FatturaElettronica('FPR12');
        $invoice->addLineItem(3);
        $invoice->setValue('DettaglioLinee[4]/NumeroLinea', 44);
        $value = $invoice->getValue('DettaglioLinee[4]/NumeroLinea');
        
        $this->assertEquals(44, $value);
    }

    public function testSetValuesFromArray()
    {
        $array =  array(
            'DatiAnagraficiVettore' => array(
                'IdFiscaleIVA' => array(
                    'IdPaese' => 'IT',
                    'IdCodice' => '09876543210'
                ),
                'Anagrafica' => array(
                    'Denominazione' => 'TRASPORTO SRLS'
                ),
                'NumeroLicenzaGuida' => 'AA090909'
            ),
            'MezzoTrasporto' => 'Mezzo',
            'CausaleTrasporto' => 'La causale del traporto',
            'NumeroColli' => '1',
            'Descrizione' => 'La descrizione'
        );

        $invoice = new FatturaElettronica('FPR12');
        $invoice->setValuesFromArray('DatiTrasporto', $array);
        $value1 = $invoice->getValue('Denominazione', 'DatiTrasporto');
        $value2 = $invoice->getValue('NumeroLicenzaGuida', 'DatiTrasporto');

        $this->assertEquals('TRASPORTO SRLS AA090909', "$value1 $value2");        
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

    /**
     * Stylesheet
     ***************************************************************************
     */
    public function testSetStylesheet()
    {
        $invoice = new FatturaElettronica('FPR12');
        $invoice->setStylesheet('fatturaPA_v1.2.1.xsl');
        $xml = $invoice->asXML();
        $string = '<?xml-stylesheet type="text/xsl" href="fatturaPA_v1.2.1.xsl"?>';

        $this->assertGreaterThan(0, strpos($xml, $string));
    }

    public function testResetStylesheet()
    {
        $invoice = new FatturaElettronica('FPR12');
        $invoice->setStylesheet('fatturaPA_v1.2.1.xsl');
        $xml = $invoice->asXML();
        $invoice->unsetStylesheet();
        $xml = $invoice->asXML();
        $string = '<?xml-stylesheet type="text/xsl" href="fatturaPA_v1.2.1.xsl"?>';

        $this->assertFalse(strpos($xml, $string));
    }
}
