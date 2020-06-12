<?php

namespace Core;

use App\Config;
use DateTimeImmutable;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\Token\Plain;

/**
 * Enclapsulate the logic for JWT authentication
 */
class JWT
{
    /**
     * Config for the JWT
     *
     * @var null | Configuration
     */
    private $_config = null;

    /**
     * Constructor
     * 
     * @return void
     */
    public function __construct()
    {
        $this->_config = Configuration::forSymmetricSigner(
            new Sha256(),
            new Key(Config::JWT_KEY)
        );
    }

    /**
     * Generate and return JWT token for user
     *
     * @param array $user
     * @return void
     */
    public function getToken($user)
    {
        $now   = new DateTimeImmutable();
        $token = $this->_config->createBuilder()
            ->issuedBy(Config::BASE_URL)
            ->permittedFor(Config::BASE_URL)
            ->identifiedBy($user['username'])
            ->issuedAt($now)
            ->canOnlyBeUsedAfter($now)
            ->expiresAt($now->modify('+1 hour'))
            ->withClaim('uid', $user['id'])
            ->getToken($this->_config->getSigner(), $this->_config->getSigningKey());
        return (string) $token;
    }

    /**
     * Whether or not the JWT token is valid
     *
     * @param string $auth
     * @return boolean
     */
    public function isValid($auth)
    {
        if (!preg_match('/Bearer\s(\S+)/', $auth, $matches)) {
            return false;
        }

        $token = $this->_config->getParser()->parse((string) $matches[1]);
        assert($token instanceof Plain);

        $constraints = $this->_config->getValidationConstraints();
        return $this->_config->getValidator()->validate($token, ...$constraints);
    }
}
