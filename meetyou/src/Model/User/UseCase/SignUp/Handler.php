<?php
declare(strict_types=1);

namespace App\Model\User\UseCase\SignUp;


use App\Mapper\User\UserMapper;
use App\Model\User\Entity\City;
use App\Model\User\Entity\Email;
use App\Model\User\Entity\Gender;
use App\Model\User\Entity\Id;
use App\Model\User\Entity\Name;
use App\Model\User\Entity\User;
use App\Model\User\Service\PasswordHasher;
use DomainException;

class Handler
{
    /**
     * @var PasswordHasher
     */
    private $hasher;
    /**
     * @var UserMapper
     */
    private $mapper;

    public function __construct(PasswordHasher $hasher, UserMapper $mapper)
    {
        $this->hasher = $hasher;
        $this->mapper = $mapper;
    }

    public function handle(Command $command): void
    {
        if ($this->mapper->findByEmail($command->email)) {
            throw new DomainException("User with email {$command->email} is already registered");
        }

        $user = User::signUp(
            Id::next(),
            new Name($command->firstname, $command->lastname),
            new City($command->city),
            $command->birthday,
            new Gender($command->gender),
            new Email($command->email),
            $this->hasher->hash($command->password)
        );

        $user->setInterests($command->interests);

        $this->mapper->insert($user);
    }
}