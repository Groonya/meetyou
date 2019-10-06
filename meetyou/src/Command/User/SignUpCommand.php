<?php
declare(strict_types=1);

namespace App\Command\User;


use App\Model\User\UseCase\SignUp\Handler;
use DateTimeImmutable;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class SignUpCommand extends Command
{
    protected static $defaultName = 'user:signup';
    /**
     * @var Handler
     */
    private $handler;
    /**
     * @var ValidatorInterface
     */
    private $validator;

    public function __construct(Handler $handler, ValidatorInterface $validator)
    {
        parent::__construct();
        $this->handler = $handler;
        $this->validator = $validator;
    }


    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $command = new \App\Model\User\UseCase\SignUp\Command();

        $command->email = $io->ask('Email');
        $command->password = $io->askHidden('Password');
        $command->firstname = $io->ask('Firstname');
        $command->lastname = $io->ask('Lastname');
        $command->birthday = new DateTimeImmutable($io->ask('Birthday (Y-m-d)'));
        $command->gender = $io->askQuestion(new ChoiceQuestion('Gender', ['Female', 'Male']));
        $command->city = $io->ask('City');

        $errors = $this->validator->validate($command);

        if (count($errors) > 0) {
            foreach ($errors as $error) {
                $io->error($error->getMessage());
            }
        }

        $this->handler->handle($command);
    }
}