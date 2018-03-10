<?php

namespace Omnipay\Novalnet\Helpers;

/**
 * Based on the Novalnet Documentation example
 *
 * Class RedirectEncode
 * @package Omnipay\Novalnet\Helpers
 */
class RedirectEncode
{

    public static function encode($data, $password, $unique_id)
    {
        $data = trim($data);
        if ($data == '') {
            throw new \InvalidArgumentException('Encode error: no data to encode');
        }
        if (!function_exists('base64_encode') or !function_exists('openssl_encrypt')) {
            throw new \Exception('Encode error: func n/a (base64_encode or openssl_encrypt)');
        }
        try {
            $data = htmlentities(base64_encode(openssl_encrypt($data, "aes-256-cbc", $password, true, $unique_id)));
        } catch (\Exception $e) {
            throw new \Exception('Encode error: Cannot encode \'' . $data .'\': ' . $e->getMessage(), 0, $e);
        }

        return $data;
    }

    public static function decode($data, $password, $unique_id)
    {
        $data = trim($data);
        if ($data == '') {
            throw new \InvalidArgumentException('Encode error: no data to decode');
        }
        if (!function_exists('base64_decode') or !function_exists('openssl_encrypt')) {
            throw new \Exception('Encode error: func n/a (base64_decode or openssl_decrypt)');
        }
        try {
            $data = openssl_decrypt(base64_decode(html_entity_decode($data)), "aes-256-cbc", $password, true, $unique_id);
        } catch (\Exception $e) {
            throw new \Exception('Encode error: Cannot decode \'' . $data .'\': ' . $e->getMessage(), 0, $e);
        }

        return $data;
    }

    public static function hash1($h, $key) #$h contains encoded data
    {
        if (!$h) {
            throw new \InvalidArgumentException('Hash Error: no data to hash');
        }
        if (!function_exists('md5')) {
            throw new \InvalidArgumentException('Hash error: md5 func n/a');
        }

        return md5(
            $h['auth_code'] .
            $h['product'] .
            $h['tariff'] .
            $h['amount'] .
            $h['test_mode'] .
            $h['uniqid'] .
            strrev($key)
        );
    }

    /**
     * @param array $response $_REQUEST from response
     * @param string $password #Merchant payment access key
     * @return bool
     */
    public static function checkHash(array $response, $password)
    {
        $params = array(
            'auth_code',
            'product',
            'tariff',
            'amount',
            'test_mode',
            'uniqid'
        );

        $h = array();
        foreach ($params as $key) {
            if (isset($response[$key . '_secure'])) {
                $h[$key] = $response[$key . '_secure'];
            } elseif (isset($response[$key])) {
                $h[$key] = $response[$key];
            } else {
                return false;
            }
        }

        if ($response['hash2'] != md5(
            $h['auth_code'] . $h['product'] . $h['tariff'] . $h['amount'] .
            $h['test_mode'] . $h['uniqid'] . strrev($password)
        )
        ) {
            return false;
        }

        return true;
    }
}
