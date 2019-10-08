<?php
declare(strict_types=1);

namespace App\Model\User\UseCase\SignUp;

use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Length(min="2", max="50")
     */
    public $firstname;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Length(min="2", max="50")
     */
    public $lastname;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Length(min="2", max="100")
     */
    public $city;

    /**
     * @var \DateTimeImmutable
     * @Assert\NotBlank()
     * @Assert\Date
     * @Assert\LessThan("-14 years")
     */
    public $birthday;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Choice({"Female", "Male"})
     */
    public $gender;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    public $email;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Length(min="2", max="100")
     */
    public $password;

    /**
     * @var string
     */
    public $interests;
}