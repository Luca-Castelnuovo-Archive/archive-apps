<?php

namespace App\Helpers;

use Exception;
use lucacastelnuovo\Helpers\JWT;

class JWTHelper
{
    private $provider;

    /**
     * Set config
     * 
     * @return void
     */
    public function __construct()
    {
        $this->provider = new JWT([
            'iss' => config('jwt.iss'),
            'aud' => config('jwt.iss'),
            'private_key' => config('jwt.private_key'),
            'public_key' => config('jwt.public_key')
        ]);
    }

    /**
     * Create JWT.
     *
     * @param array  $data
     * @param string $exp
     * @param string $aud optional
     *
     * @return string
     */
    public static function create($data, $exp, $aud = null) // TODO: rewrite all jwt creates
    {
        return $this->provider->create(
            $data,
            $exp,
            $aud
        );
    }

    /**
     * Decode and validate JWT.
     *
     * @param string $type
     * @param string $token
     *
     * @return array
     * @throws Exception
     */
    public static function valid($type, $token)
    {
        if (!$token) {
            throw new Exception('Token not provided');
        }

        $claims = $this->provider->valid($token);

        if ($type !== $claims->type) {
            throw new Exception('Token type not valid');
        }

        return $claims;
    }
}
