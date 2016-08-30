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

    public static function encode($data, $password)
    {
        $data = trim($data);
        if ($data == '') {
            throw new \InvalidArgumentException('Encode error: no data to encode');
        }
        if (!function_exists('base64_encode') or !function_exists('pack') or !function_exists('crc32')) {
            throw new \Exception('Encode error: func n/a (base64_encode, pack or crc32)');
        }
        try {
            $crc = sprintf('%u', crc32($data));# %u is a must for ccrc32 returns a signed value
            $data = $crc . "|" . $data;
            $data = bin2hex($data . $password);
            $data = strrev(base64_encode($data));
        } catch (\Exception $e) {
            throw new \Exception('Encode error: Cannot encode \'' . $data .'\': ' . $e->getMessage(), 0, $e);
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

    public static function encodeParams($auth_code, $product_id, $tariff_id, $amount, $test_mode, $uniqid, $password)
    {
        $auth_code = self::encode($auth_code, $password);
        $product_id = self::encode($product_id, $password);
        $tariff_id = self::encode($tariff_id, $password);
        $amount = self::encode($amount, $password);
        $test_mode = self::encode($test_mode, $password);
        $uniqid = self::encode($uniqid, $password);

        $hash = self::hash1(array(
            'auth_code' => $auth_code,
            'product' => $product_id,
            'tariff' => $tariff_id,
            'amount' => $amount,
            'test_mode' => $test_mode,
            'uniqid' => $uniqid,
        ), $password);

        return array($auth_code, $product_id, $tariff_id, $amount, $test_mode, $uniqid, $hash);
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
