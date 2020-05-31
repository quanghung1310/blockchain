<?php

namespace App\Blockchain;

class Wallet
{
    public static function generateKeyPair()
    {
        $params = [
            'private_key_bits' => 2048,
            'private_key_type' => OPENSSL_KEYTYPE_RSA,
        ];

        $res = openssl_pkey_new($params);
        openssl_pkey_export($res, $privKey);

        $pubKey = openssl_pkey_get_details($res);
        $pubKey = $pubKey["key"];

        openssl_free_key($res);

        return ['private'=> $privKey, 'public'=> $pubKey, 'coin' => 100];
    }

    public static function encrypt($data, $privateKey)
    {
        openssl_private_encrypt($data, $encrypted, $privateKey);
        return base64_encode($encrypted);
    }

    public static function decrypt($encrypted, $pubKey)
    {
        openssl_public_decrypt(base64_decode($encrypted), $decrypted, $pubKey);
        return $decrypted;
    }

    public static function isValid($data, $encrypted, $pubKey): bool
    {
        return $data == self::decrypt($encrypted, $pubKey);
    }
}
