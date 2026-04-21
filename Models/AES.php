<?php

class AES
{
    private $key;
    private $methode;
    private $chaineOctet;

    /**
     * @param $key
     */
    public function __construct($key)
    {
        $this->key = $key ?? '';
        $this->methode = 'aes-256-cbc';
        //generation d'une chaine d'octet a partir d'un nombre
        $this->chaineOctet = chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0);
    }
 /*   function encrypt($mot) {
        // $sSalt = '20adeb83e85f03cfc84d0fb7e5f4d290';
        $sSalt = substr(hash('sha256', $this->key, true), 0, 32);
      

        $encrypted = base64_encode(openssl_encrypt($mot ?? '', $this->methode, $sSalt, OPENSSL_RAW_DATA, $this->chaineOctet));
        return $encrypted;
    }
*/
    public function encrypt($mot): ?string
    {
        $sSalt = substr(hash('sha256', $this->key, true), 0, 32);

        $encrypted = openssl_encrypt($mot ?? '', $this->methode, $sSalt, OPENSSL_RAW_DATA, $this->chaineOctet);

        return $encrypted !== false ? base64_encode($encrypted) : null;
    }
    
    /*function decrypt($mot) {
        //$sSalt = '20adeb83e85f03cfc84d0fb7e5f4d290';
        $sSalt = substr(hash('sha256', $this->key, true), 0, 32);
        $decrypted = openssl_decrypt(base64_decode($mot ?? ''), $this->methode, $sSalt, OPENSSL_RAW_DATA, $this->chaineOctet);
        return $decrypted;
    }*/
    
 public function decrypt(?string $mot): ?string
    {
        if (!is_string($mot) || empty($mot)) {
            return null;
        }

        $sSalt = substr(hash('sha256', $this->key, true), 0, 32);

        $decoded = base64_decode($mot, true);
        if ($decoded === false) {
            return null;
        }
        

        $decrypted = openssl_decrypt($decoded, $this->methode, $sSalt, OPENSSL_RAW_DATA, $this->chaineOctet);

        return $decrypted !== false ? $decrypted : null;
    }



}
