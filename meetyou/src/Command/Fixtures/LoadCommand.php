<?php
declare(strict_types=1);

namespace App\Command\Fixtures;


use App\Mapper\User\UserMapper;
use App\Model\User\Entity\City;
use App\Model\User\Entity\Email;
use App\Model\User\Entity\Gender;
use App\Model\User\Entity\Id;
use App\Model\User\Entity\Name;
use App\Model\User\Entity\User;
use App\Model\User\Service\PasswordHasher;
use DateTimeImmutable;
use Faker\Factory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class LoadCommand extends Command
{
    protected static $defaultName = 'fixtures:load';
    /**
     * @var UserMapper
     */
    private $mapper;
    /**
     * @var PasswordHasher
     */
    private $hasher;

    public function __construct(UserMapper $mapper, PasswordHasher $hasher)
    {
        parent::__construct();
        $this->mapper = $mapper;
        $this->hasher = $hasher;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $faker = Factory::create();
        $password = $this->hasher->hash('123');


        for ($k = 0; $k < 100; $k++) {
            $users = [];
            for ($i = 0; $i < 1000; $i++) {
                $gender = [Gender::male(), Gender::female()];

                $user = new User(
                    Id::next(),
                    new Name($faker->firstName, $faker->lastName),
                    new City($faker->city),
                    new DateTimeImmutable($faker->date()),
                    $faker->randomElement($gender),
                    new Email($k.$i.$faker->email),
                    $password
                );

                $user->setInterests($faker->realText());
                $users[] = $user;
            }

            $this->mapper->runInTransaction(function () use (&$users) {
                array_walk($users, function ($user) {
                    $this->mapper->insert($user);
                });
            });
        }
    }


}