<?php namespace Taocomp\Sdicoop;

class Invoice extends Document
{
    // Templates
    protected static $templates = array();

    // Client object, if any
    protected static $client = null;

    // --------------------------------------------------------------
    // Set company data
    // --------------------------------------------------------------
    public function setCompanyData( array $data )
    {
        $IdTrasmittente = $this->FatturaElettronicaHeader->DatiTrasmissione->IdTrasmittente;
        $IdTrasmittente->IdCodice = $data['IdCodice'];
        $IdTrasmittente->IdPaese  = $data['IdPaese'];

        $DatiAnagrafici = $this->FatturaElettronicaHeader->CedentePrestatore->DatiAnagrafici;
        $DatiAnagrafici->Anagrafica->Denominazione = $data['Denominazione'];
        $DatiAnagrafici->IdFiscaleIVA->IdPaese     = $data['IdPaese'];
        $DatiAnagrafici->IdFiscaleIVA->IdCodice    = $data['IdCodice'];
        $DatiAnagrafici->RegimeFiscale             = $data['RegimeFiscale'];

        $Sede = $this->FatturaElettronicaHeader->CedentePrestatore->Sede;
        $Sede->Indirizzo = $data['Indirizzo'];
        $Sede->CAP       = $data['CAP'];
        $Sede->Comune    = $data['Comune'];
        $Sede->Provincia = $data['Provincia'];
        $Sede->Nazione   = $data['Nazione'];

        return $this;
    }

    // --------------------------------------------------------------
    // Factory
    // --------------------------------------------------------------    
    public static function factory( string $template, array $company = array() )
    {
        $obj = parent::factory($template);

        // If $template is a file (i.e. an invoice), don't change data
        if (is_readable($template)) {
            return $obj;
        }

        // Company data
        if (!empty($company)) {
            $obj->setCompanyData($company);
        }

        // Invoice format
        $obj->FatturaElettronicaHeader->DatiTrasmissione->FormatoTrasmissione = $template;

        return $obj;
    }

    // --------------------------------------------------------------
    // Add node PECDestinatario and set value
    // --------------------------------------------------------------
    public function setPECDestinatario( string $value )
    {
        // Add node if not present
        if (!isset($this->FatturaElettronicaHeader->DatiTrasmissione->PECDestinatario)) {
            $this->FatturaElettronicaHeader->DatiTrasmissione->addChild('PECDestinatario');
        }
        
        $this->FatturaElettronicaHeader->DatiTrasmissione->PECDestinatario = $value;

        return $this;
    }

    // --------------------------------------------------------------
    // Unset node PECDestinatario
    // --------------------------------------------------------------
    public function unsetPECDestinatario()
    {
        if (isset($this->FatturaElettronicaHeader->DatiTrasmissione->PECDestinatario)) {
            unset($this->FatturaElettronicaHeader->DatiTrasmissione->PECDestinatario);
        }

        return $this;
    }

    // --------------------------------------------------------------
    // Create a new notification from current invoice
    // --------------------------------------------------------------
    public function prepareNotification( $template )
    {
        $DatiGeneraliDocumento = $this->FatturaElettronicaBody->DatiGenerali->DatiGeneraliDocumento;
        $NumeroFattura = (string)$DatiGeneraliDocumento->Numero;
        $AnnoFattura = substr((string)$DatiGeneraliDocumento->Data, 0, 4);

        $notification = Notification::factory($template);
        $notification->RiferimentoFattura->NumeroFattura = $NumeroFattura;
        $notification->RiferimentoFattura->AnnoFattura = $AnnoFattura;
        $notification->Esito = Notification::EC01;

        return $notification;
    }

    // --------------------------------------------------------------
    // Get invoice's filename
    // --------------------------------------------------------------
    public function getNomeFile()
    {
        $id = (string)$this->FatturaElettronicaHeader->DatiTrasmissione->ProgressivoInvio;
        $IdTrasmittente = $this->FatturaElettronicaHeader->DatiTrasmissione->IdTrasmittente;
        $codice = (string)$IdTrasmittente->IdCodice;
        $paese = (string)$IdTrasmittente->IdPaese;

        if (!$id) {
            throw new \Exception(__FUNCTION__ . ': ProgressivoInvio is empty');
        }
        if (!$paese) {
            throw new \Exception(__FUNCTION__ . ': IdPaese is empty');
        }
        if (!$codice) {
            throw new \Exception(__FUNCTION__ . ': IdCodice is empty');
        }

        return "{$paese}{$codice}_$id.xml";
    }

    // --------------------------------------------------------------
    // Send invoice to SdI
    // --------------------------------------------------------------
    public function send()
    {
        $fileSdIBase = new FileSdIBase();
        $fileSdIBase->NomeFile = $this->getNomeFile();
        $fileSdIBase->File = $this->asXml();
        $fileSdIBase->encodeFile();
        
        return new RispostaSdIRiceviFile(self::$client->RiceviFile($fileSdIBase));
    }
}
