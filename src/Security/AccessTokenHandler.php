<?php

namespace App\Security;

use App\Repository\ApiTokenRepository;
use JetBrains\PhpStorm\NoReturn;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Http\AccessToken\AccessTokenHandlerInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;

class AccessTokenHandler implements AccessTokenHandlerInterface
{
    private string $token;

    #[NoReturn]
    public function __construct($token)
    {
        $this->token = $token;
    }

    public function getUserBadgeFrom(string $accessToken): UserBadge
    {
        if ($this->token !== $accessToken) {
            throw new BadCredentialsException('Invalid credentials.');
        }

        return new UserBadge(uniqid("API-", true));
    }
}
