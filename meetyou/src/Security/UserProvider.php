<?php

namespace App\Security;

use App\Mapper\User\UserMapper;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class UserProvider implements UserProviderInterface
{
    /**
     * @var UserMapper
     */
    private $mapper;

    public function __construct(UserMapper $mapper)
    {
        $this->mapper = $mapper;
    }

    public function loadUserByUsername($email): UserInterface
    {
        if (!($user = $this->mapper->findByEmail($email))) {
            throw new UsernameNotFoundException('User not found');
        }

        return new User(
            $user->getEmail()->getValue(),
            $user->getPasswordHash(),
            $user->getName()->full()
        );
    }

    public function refreshUser(UserInterface $user): UserInterface
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Invalid user class "%s".', get_class($user)));
        }

        return $this->loadUserByUsername($user->getUsername());
    }

    public function supportsClass($class)
    {
        return User::class === $class;
    }
}
