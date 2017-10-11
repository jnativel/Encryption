<?php

namespace jnativel\Encryption;

class Encryption
{

    /**
     * The encryption master key
     * @var null|string
     */
    private $masterKey;

    /**
     * The algorithm used for encryption
     * @var string
     */
    protected $cipher;

    /**
     * Encryption constructor.
     * @param string|null $masterKey
     * @param string $cipher
     */
    public function __construct(string $masterKey = null, $cipher = "AES-256-CBC")
    {
        if ($this->supported($cipher))
        {
            $this->cipher = $cipher;
            $this->setMasterKey($masterKey);
        }else{
            throw new \RuntimeException("The ciphers (".$cipher.") is not supported");
        }
    }

    /**
     * Set the encryption master key
     * @param string $masterKey
     * @return bool
     */
    public function setMasterKey(string $masterKey = null){
        if(!is_null($masterKey)){
            $this->masterKey = (is_null($masterKey)) ? $this->generateKey(32) : $masterKey;
        }
        return true;
    }

    /**
     * Get the encryption master key
     * @return null|string
     */
    public function getMasterKey(){
        return $this->masterKey;
    }

    /**
     * Generate a encryption new master key
     * @param int $length
     * @param bool $upper
     * @param bool $number
     * @param bool $symbol
     * @return string
     */
    public function generateKey(int $length = 12, bool $upper = true, bool $number = true, bool $symbol = true): string
    {
        $masterKeyString = [
            'alpha' => 'abcdefghijkmnopqrstuvwxyz',
            'number' => '0123456789',
            'symbol' => '#!?$%&*@-_+='
        ];
        $string = [];
        if($upper){
            $string[] = $this->get_string(strtoupper($masterKeyString['alpha']), ceil($length * 30 / 100));
        }
        if($number){
            $string[] = $this->get_string($masterKeyString['number'], ceil($length * 20 / 100));
        }
        if($symbol){
            $string[] = $this->get_string($masterKeyString['symbol'], ceil($length * 10 / 100));
        }
        $string = implode('', array_map(function($string){ return implode('', $string);}, $string));
        $string = str_split($string);
        $masterKey = array_slice($this->get_string($masterKeyString['alpha'], $length), 0, $length - count($string));
        $masterKey = array_merge($masterKey, $string);
        $masterKey = str_shuffle(implode('', $masterKey));
        return $masterKey;
    }

    /**
     * Encrypt the given value
     * @param string $value
     * @param string|null $masterKey
     * @return string
     */
    public function encrypt(string $value, string $masterKey = null): string
    {
        $this->setMasterKey($masterKey);
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($this->cipher));
        $value = openssl_encrypt($value, $this->cipher, $this->masterKey, 0, $iv);
        return $this->safe_b64encode($value.':'.base64_encode($iv));
    }

    /**
     * Decrypt the given value
     * @param string $value
     * @param string|null $masterKey
     * @return null|string
     */
    public function decrypt(string $value, string $masterKey = null): ?string
    {
        $this->setMasterKey($masterKey);
        $value = explode(':', $this->safe_b64decode($value));
        if(count($value) == 2){
            return openssl_decrypt($value[0], $this->cipher, $this->masterKey, $options=0, base64_decode($value[1]));
        }else{
            return null;
        }
    }

    /**
     * Determine if the given cipher is valid
     * @param  string  $cipher
     * @return bool
     */
    private function supported($cipher)
    {
        $ciphers_and_aliases = openssl_get_cipher_methods(true);
        if(in_array($cipher, array_values($ciphers_and_aliases))){
            return true;
        }else{
            return false;
        }
    }

    private function get_string($string, $length): array
    {
        $tmp = [];
        for($i = 0; $i< $length; $i++){
            array_push($tmp, substr($string, mt_rand(0, strlen($string) - 1), 1));
        }
        return $tmp;
    }

    private function safe_b64encode($string): string
    {
        $string = base64_encode($string);
        $string = str_replace(array('+','/','='), array('-','_',''), $string);
        return trim($string);
    }

    private function safe_b64decode($string): string
    {
        $string = str_replace(array('-','_'),array('+','/'),$string);
        $mod4 = strlen($string) % 4;
        if ($mod4) {
            $string .= substr('====', $mod4);
        }
        return trim(base64_decode($string));
    }

}


