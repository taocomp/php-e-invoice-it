# Validatore di XML della Fattura Elettronica

[![Build Status](https://travis-ci.org/Slamdunk/php-validatore-fattura-elettronica.svg?branch=master)](https://travis-ci.org/Slamdunk/php-validatore-fattura-elettronica)
[![Code Coverage](https://scrutinizer-ci.com/g/Slamdunk/php-validatore-fattura-elettronica/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/Slamdunk/php-validatore-fattura-elettronica/?branch=master)
[![Packagist](https://img.shields.io/packagist/v/slam/php-validatore-fattura-elettronica.svg)](https://packagist.org/packages/slam/php-validatore-fattura-elettronica)

## Installazione

```
composer require slam/php-validatore-fattura-elettronica
```

## Utilizzo

```php
use SlamFatturaElettronica\Validator;

$feValidator = new Validator();
$feValidator->assertValidXml('<xml ...>');

// In caso di struttura XML errata, viene lanciata una
//      SlamFatturaElettronica\Exception\InvalidXmlStructureException
// In caso di XML valido ma non aderente all'XSD, viene lanciata una
//      SlamFatturaElettronica\Exception\InvalidXsdStructureComplianceException
```

## Riferimenti

I due siti di riferimento sono al momento:

1. https://www.agenziaentrate.gov.it/wps/content/Nsilib/Nsi/Schede/Comunicazioni/Fatture+e+corrispettivi/Fatture+e+corrispettivi+ST/ST+invio+di+fatturazione+elettronica/?page=schedecomunicazioni
1. http://www.fatturapa.gov.it/export/fatturazione/it/normativa/f-2.htm

Gli XSD usati da questa libreria sono quelli presi dal primo dei due siti,
ovvero `www.agenziaentrate.gov.it`, che a dispetto del numero di versione
esplicitato sembra quello più aggiornato (vedi ad esempio tra i tipi di
documento la differenza su `Autofattura`).
