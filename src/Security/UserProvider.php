<?php

namespace App\Security;

use App\Entity\User;
use Exception;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class UserProvider implements UserProviderInterface
{
    /**
     * Symfony calls this method if you use features like switch_user
     * or remember_me. If you're not using these features, you do not
     * need to implement this method.
     *
     * @throws UserNotFoundException|Exception if the user is not found
     */
    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        $user = new User();
        $user->setRoles(['ROLE_ADMIN']);

        return $user;
    }

    /**
     * @throws Exception
     */
    public function refreshUser(UserInterface $user): UserInterface
    {
        throw new Exception('TODO: fill in refreshUser() inside ' . __FILE__);
    }

    /**
     * Tells Symfony to use this provider for this User class.
     */
    public function supportsClass(string $class): bool
    {
        return User::class === $class || is_subclass_of($class, User::class);
    }
}
