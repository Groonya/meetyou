<?php
declare(strict_types=1);

namespace App\Mapper\User;


use App\Model\User\Entity\User;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\FetchMode;

class UserMapper
{
    /**
     * @var Connection
     */
    private $connection;


    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function findByEmail(string $email): ?User
    {
        $sql = 'SELECT * FROM users WHERE email = :email';
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue('email', $email);
        $stmt->execute();
        $row = $stmt->fetch(FetchMode::ASSOCIATIVE);

        if ($row === false) {
            return null;
        }

        return $this->map($row);
    }

    private function map($row): User
    {
        return User::fromState($row);
    }

    public function insert(User $user): void
    {
        $sql = 'INSERT INTO users (id, email, password_hash, name_first, name_last, birthday, gender, interests, city) '
            .'VALUE (:id, :email, :password_hash, :name_first, :name_last, :birthday, :gender, :interests, :city)';
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue('id', $user->getId()->getId());
        $stmt->bindValue('email', $user->getEmail()->getValue());
        $stmt->bindValue('password_hash', $user->getPasswordHash());
        $stmt->bindValue('name_first', $user->getName()->getFirst());
        $stmt->bindValue('name_last', $user->getName()->getLast());
        $stmt->bindValue('birthday', $user->getBirthday()->format('Y-m-d'));
        $stmt->bindValue('gender', $user->getGender()->getValue());
        $stmt->bindValue('interests', $user->getInterests());
        $stmt->bindValue('city', $user->getCity()->getName());
        $stmt->execute();
    }
}