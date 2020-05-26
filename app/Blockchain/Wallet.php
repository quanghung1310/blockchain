<?php

namespace App\Blockchain;

class Wallet
{
    public static function generateKeyPair()
    {
        $res = openssl_pkey_new([
            'private_key_bits' => 4096,
            'private_key_type' => OPENSSL_KEYTYPE_RSA,
        ]);

        openssl_pkey_export($res, $private);
        return [$private, openssl_pkey_get_details($res)['key']];
    }

    public static function encrypt($mesage, $privKey)
    {
        openssl_private_encrypt($mesage, $crypted, $privKey);
        return base64_encode($crypted);
    }

    public static function decrypt($crypted, $pubKey)
    {
        openssl_public_decrypt(base64_decode($crypted), $decrypted, $pubKey);
        return $decrypted;
    }

    public static function isValid($message, $crypted, $pubKey): bool
    {
        return $message == self::decrypt($crypted, $pubKey);
    }
}
