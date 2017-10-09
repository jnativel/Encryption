<?php

require "src/Encryption/Encryption.php";
use jnativel\Encryption\Encryption;

$encryption = new Encryption();

// Generate a new  Key
$masterKey = $encryption->generateKey();
var_dump($masterKey);

// Encrypt a string
$string = "my-string";
$encrypt = $encryption->encrypt($string, $masterKey);
var_dump($encrypt);

// Decrypt a string
$decrypt = $encryption->decrypt($encrypt, $masterKey);
var_dump($decrypt);
