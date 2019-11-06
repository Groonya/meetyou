<?php
declare(strict_types=1);

namespace App\ReadModel\User;


use App\Mapper\User\UserMapper;
use App\Model\User\Entity\User;
use App\ReadModel\User\Filter\Filter;
use DateInterval;
use Doctrine\DBAL\Connection;
use InvalidArgumentException;
use Knp\Component\Pager\Event\Subscriber\Paginate\Callback\CallbackPagination;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use PDO;
use Psr\SimpleCache\CacheInterface;

class UserFetcher
{
    private const CACHE_COUNT_KEY = 'users_count';
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
    private $cache;

    public function __construct(
        Connection $connection,
        UserMapper $mapper,
        PaginatorInterface $paginator,
        CacheInterface $cache
    ) {
        $this->connection = $connection;
        $this->paginator = $paginator;
        $this->mapper = $mapper;
        $this->cache = $cache;
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
            throw new InvalidArgumentException('Name can\'t be blank');
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
        if (($count = $this->cache->get(self::CACHE_COUNT_KEY)) === null) {
            $stmt = $this->connection->prepare('SELECT COUNT(*) FROM users');
            $stmt->execute();

            $count = $stmt->fetchColumn();

            $this->cache->set(self::CACHE_COUNT_KEY, $count, new DateInterval('P1D'));
        }

        return $count;
    }

    private function fetchAll(int $offset, int $limit): array
    {
        $stmt = $this->connection->prepare("SELECT id, name_first, name_last FROM users ORDER BY id LIMIT $offset, $limit");
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


}