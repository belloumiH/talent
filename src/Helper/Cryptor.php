<?php

namespace App\Helper;

class Cryptor
{
    private $key = 'e5zGd4fyQ72dSdV7jvt5NMEaD2z3WjCj8YK946DRt2E72';

    public static function encrypt(string $value = '')
    {
        $key = (new self())->getKey();
        $ivlen = openssl_cipher_iv_length($cipher = 'aes-256-cbc');
        $iv = openssl_random_pseudo_bytes($ivlen);
        $ciphertext_raw = openssl_encrypt($value, $cipher, $key, $options = OPENSSL_RAW_DATA, $iv);
        $hmac = hash_hmac('sha256', $ciphertext_raw, $key, $as_binary = true);
        $ciphertext = base64_encode($iv.$hmac.$ciphertext_raw);

        return $ciphertext;
    }

    public static function decrypt(string $value = '')
    {
        $key = (new self())->getKey();
        $c = base64_decode($value);
        $ivlen = openssl_cipher_iv_length($cipher = 'aes-256-cbc');
        $iv = substr($c, 0, $ivlen);
        $hmac = substr($c, $ivlen, $sha2len = 32);
        $value_raw = substr($c, $ivlen + $sha2len);
        $original_plaintext = openssl_decrypt($value_raw, $cipher, $key, $options = OPENSSL_RAW_DATA, $iv);
        $calcmac = hash_hmac('sha256', $value_raw, $key, $as_binary = true);
        if (hash_equals($hmac, $calcmac)) {//PHP 5.6+ timing attack safe comparison
            return $original_plaintext;
        }

        return '';
    }

    public function getKey(): string
    {
        return $this->key;
    }
}
