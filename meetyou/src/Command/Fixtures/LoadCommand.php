<?php
declare(strict_types=1);

namespace App\Command\Fixtures;


use App\Model\User\UseCase\SignUp\Handler;
use DateTimeImmutable;
use Faker\Factory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class LoadCommand extends Command
{
    protected static $defaultName = 'fixtures:load';
    /**
     * @var Handler
     */
    private $handler;

    public function __construct(Handler $handler)
    {
        parent::__construct();
        $this->handler = $handler;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $faker = Factory::create();

        for ($i = 0; $i < 1000; $i++) {
            $command = new \App\Model\User\UseCase\SignUp\Command();
            $command->birthday = new DateTimeImmutable($faker->date('Y-m-d'));
            $command->email = $faker->unique()->email;
            $command->firstname = $faker->firstName;
            $command->lastname = $faker->lastName;
            $command->city = $faker->city;
            $command->gender = $faker->randomElement(['male', 'female']);
            $command->password = $faker->password;

            $this->handler->handle($command);
        }
    }


}