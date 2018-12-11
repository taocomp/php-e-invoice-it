<?php namespace Taocomp\Sdicoop;

class Notification extends Document
{
    const EC01 = 'EC01';
    const EC02 = 'EC02';
    
    // Templates
    protected static $templates = array();

    // Client object, if any
    protected static $client = null;

    public static function factory( string $template )
    {
        $obj = parent::factory($template);

        // Remove optional nodes
        if (isset($obj->Descrizione)) {
            unset($obj->Descrizione);
        }
        if (isset($obj->MessageIdCommittente)) {
            unset($obj->MessageIdCommittente);
        }
        if (isset($obj->RiferimentoFattura->PosizioneFattura)) {
            unset($obj->RiferimentoFattura->PosizioneFattura);
        }

        return $obj;
    }
    
    // --------------------------------------------------------------
    // Send notification to SdI
    // --------------------------------------------------------------
    public function send( string $filename )
    {
        $fileSdI = new FileSdI();
        $fileSdI->IdentificativoSdI = $this->IdentificativoSdI;
        $fileSdI->NomeFile = basename($filename);
        $fileSdI->File = $this->asXml();
        $fileSdI->encodeFile();
        
        return new RispostaSdINotificaEsito(self::$client->NotificaEsito($fileSdI));
    }
}
