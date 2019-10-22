<?php
declare(strict_types=1);

namespace App\ReadModel\User;


use App\Mapper\User\UserMapper;
use App\Model\User\Entity\User;
use App\ReadModel\User\Filter\Filter;
use Doctrine\DBAL\Connection;
use Knp\Component\Pager\Event\Subscriber\Paginate\Callback\CallbackPagination;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use PDO;

class UserFetcher
{
    /**
     * @var Connection
     */
    private $connection;
    /**
     * @var PaginatorInterface
     */
    private $paginator;
    /**
     * @var UserMapper
     */
    private $mapper;

    public function __construct(Connection $connection, UserMapper $mapper, PaginatorInterface $paginator)
    {
        $this->connection = $connection;
        $this->paginator = $paginator;
        $this->mapper = $mapper;
    }

    public function all(int $page, int $limit): PaginationInterface
    {
        $callback = new CallbackPagination(function () {
            return $this->countAll();
        }, function (int $offset, int $limit) {
            return $this->fetchAll($offset, $limit);
        });

        return $this->paginator->paginate($callback, $page, $limit);
    }

    public function search(int $page, int $limit, Filter $filter): PaginationInterface
    {
        if (!$filter->name) {
            throw new \InvalidArgumentException('Name can\'t be blank');
        }
        $callback = new CallbackPagination(function () use ($filter) {
            return $this->countByName($filter->name);
        }, function (int $offset, int $limit) use ($filter) {
            return $this->fetchByName($offset, $limit, $filter->name);
        });

        return $this->paginator->paginate($callback, $page, $limit);
    }

    private function countByName(string $name)
    {
        $stmt = $this->connection->prepare('SELECT COUNT(*) FROM users WHERE name_first LIKE :name OR name_last LIKE :name');
        $stmt->bindValue('name', "$name%");
        $stmt->execute();

        return $stmt->fetchColumn();
    }

    private function fetchByName(int $offset, int $limit, string $name): array
    {
        $stmt = $this->connection->prepare("SELECT u.id, u.name_first, u.name_last FROM users u WHERE u.name_first LIKE :name OR name_last LIKE :name ORDER BY u.id LIMIT $offset, $limit");
        $stmt->bindValue('name', "$name%");
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findById(string $id): ?User
    {
        return $this->mapper->findById($id);
    }

    private function countAll()
    {
        $stmt = $this->connection->prepare('SELECT COUNT(*) FROM users');
        $stmt->execute();

        return $stmt->fetchColumn();
    }

    private function fetchAll(int $offset, int $limit): array
    {
        $stmt = $this->connection->prepare("SELECT id, name_first, name_last FROM users ORDER BY id LIMIT $offset, $limit");
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


}