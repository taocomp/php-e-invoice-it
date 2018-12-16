<?php

use \Taocomp\EinvoiceIt\FatturaElettronica;

try
{
    require_once(__DIR__ . '/../autoload.php');

    $invoice = new FatturaElettronica('FPR12');
    $invoice->load('IT02313821007_10001.xml');
    $invoice->setValue('ProgressivoInvio', 10002);
    $invoice->save();
}
catch (\Exception $e)
{
    echo $e->getMessage() . PHP_EOL;
}
