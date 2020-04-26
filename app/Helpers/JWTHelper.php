<?php

namespace App\Helpers;

use Exception;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;

class JWTHelper
{
    /**
     * Create JWT.
     *
     * @param string $type
     * @param array  $data
     * @param string    $aud optional
     *
     * @return string
     */
    public static function create($type, $data, $aud = null)
    {
        $config = config('jwt')[$type];
        $aud = $aud ?: $config->aud;

        $head = [
            'iss' => config('jwt.iss'),
            'aud' => $aud,
            'iat' => time(),
            'exp' => time() + $config->exp,
            'type' => $config->type
        ];

        $payload = array_merge($head, $data);

        return JWT::encode(
            $payload,
            config('jwt.secret'),
            config('jwt.algorithm')
        );
    }

    /**
     * Decode and validate JWT.
     *
     * @param string $type
     * @param string $token
     *
     * @return bool
     */
    public static function valid($type, $token)
    {
        if (!$token) {
            throw new Exception('Token not provided');
        }

        // TODO: get config and validate aud, type

        try {
            $credentials = JWT::decode(
                $token,
                config('jwt.secret'),
                [config('jwt.algorithm')]
            );
        } catch (ExpiredException $e) {
            throw new Exception('Token has expired');
        } catch (Exception $e) {
            throw new Exception('Token is invalid');
        }

        if ($type !== $credentials->type) {
            throw new Exception('Token type not valid');
        }

        return $credentials;
    }
}
