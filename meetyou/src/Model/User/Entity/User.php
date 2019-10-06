<?php
declare(strict_types=1);

namespace App\Model\User\Entity;

use DateTimeImmutable;

class User
{
    /**
     * @var Name
     */
    private $name;
    /**
     * @var DateTimeImmutable
     */
    private $birthday;
    /**
     * @var Gender
     */
    private $gender;
    /**
     * @var Email
     */
    private $email;
    /**
     * @var string
     */
    private $passwordHash;
    /**
     * @var string
     */
    private $interests = '';
    /**
     * @var City
     */
    private $city;
    /**
     * @var Id
     */
    private $id;

    public function __construct(
        Id $id,
        Name $name,
        City $city,
        DateTimeImmutable $birthday,
        Gender $gender,
        Email $email,
        string $passwordHash
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->birthday = $birthday;
        $this->gender = $gender;
        $this->email = $email;
        $this->passwordHash = $passwordHash;
        $this->city = $city;
    }

    public static function signUp(
        Id $id,
        Name $name,
        City $city,
        DateTimeImmutable $birthday,
        Gender $gender,
        Email $email,
        string $passwordHash
    ): User {
        return new self($id, $name, $city, $birthday, $gender, $email, $passwordHash);
    }

    public static function fromState(array $data): User
    {
        $model = new self(
            new Id($data['id']),
            new Name($data['name_first'], $data['name_last']),
            new City($data['city']),
            new DateTimeImmutable($data['birthday']),
            new Gender($data['gender']),
            new Email($data['email']),
            $data['password_hash']
        );

        $model->interests = $data['interests'];

        return $model;
    }

    public function setInterests(string $interest): void
    {
        $this->interests = $interest;
    }

    /**
     * @return Name
     */
    public function getName(): Name
    {
        return $this->name;
    }

    public function getBirthday(): DateTimeImmutable
    {
        return $this->birthday;
    }

    /**
     * @return Gender
     */
    public function getGender(): Gender
    {
        return $this->gender;
    }

    /**
     * @return Email
     */
    public function getEmail(): Email
    {
        return $this->email;
    }

    public function getInterests(): string
    {
        return $this->interests;
    }

    public function getCity(): City
    {
        return $this->city;
    }

    public function getPasswordHash(): string
    {
        return $this->passwordHash;
    }

    public function getId(): Id
    {
        return $this->id;
    }
}