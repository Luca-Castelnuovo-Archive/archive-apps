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
     * @param int    $expires optional
     *
     * @return string
     */
    public static function create($type, $data, $expires = null)
    {
        $expires = $expires ?: config('jwt.ttl');
        $head = [
            'iss' => config('jwt.iss'),
            'iat' => time(),
            'exp' => time() + $expires,
            'type' => $type
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
