# Encryption

A small PHP class for create, encrypting or decrypting your passwords

## Installation using Composer

```bash
$ composer require jnativel/Encryption
```

## Usage

You can use the "generateKey()" method for generate a safe password.
You can set the lenght and diffrents type of characters (upper, number, symbol)
```php
<?php
require 'vendor/autoload.php';
$encryption = new jnativel\Encryption\Encryption();
$password = $encryption->generateKey(16, true, true, true);
var_dump($password);
```

You must define a master key for encrypt and decrypt your password or your character string. 
Example of use by passing the master key directly by the methods:

```php
<?php
require 'vendor/autoload.php';
$masterKey = "my-secret-key";
$encryption = new jnativel\Encryption\Encryption();
$password = $encryption->generateKey();
$str_encrypt = $encryption->encrypt($password, $masterKey);
$str_decrypt = $encryption->decrypt($str_encrypt, $masterKey);
var_dump($encryption->getMasterKey());
var_dump($str_encrypt);
var_dump($str_decrypt);
```

If you wish, you can also by passing the master key by the constructor

```php
<?php
require 'vendor/autoload.php';
$masterKey = "my-secret-key";
$secret = "my-secret-string";
$encryption = new jnativel\Encryption\Encryption($masterKey);
$str_encrypt = $encryption->encrypt($secret);
$str_decrypt = $encryption->decrypt($str_encrypt);
var_dump($encryption->getMasterKey());
var_dump($str_encrypt);
var_dump($str_decrypt);
```