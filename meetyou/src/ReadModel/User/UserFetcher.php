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
use Psr\Cache\CacheItemPoolInterface;

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
        CacheItemPoolInterface $cache
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
        $sql = 'SELECT COUNT(*) FROM users WHERE name_first LIKE :name OR name_last LIKE :name';

        return $this->connection->executeQuery($sql, ['name' => "$name%"])->fetchColumn();
    }

    private function fetchByName(int $offset, int $limit, string $name): array
    {
        $sql = "SELECT u.id, u.name_first, u.name_last FROM users u WHERE u.name_first LIKE :name OR name_last LIKE :name ORDER BY u.id LIMIT $offset, $limit";

        return $this->connection->executeQuery($sql, ['name' => "$name%"])->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findById(string $id): ?User
    {
        return $this->mapper->findById($id);
    }

    private function countAll()
    {
        $cacheItem = $this->cache->getItem(self::CACHE_COUNT_KEY);
        $count = $cacheItem->get();

        if ($count === null) {
            $count = $this->connection->executeQuery('SELECT COUNT(*) FROM users')->fetchColumn();
            $cacheItem->set($count)->expiresAfter(new DateInterval('P1D'));
            $this->cache->save($cacheItem);
        }

        return $count;
    }

    private function fetchAll(int $offset, int $limit): array
    {
        return
            $this->connection
                ->executeQuery("SELECT id, name_first, name_last FROM users ORDER BY id LIMIT $offset, $limit")
                ->fetchAll(PDO::FETCH_ASSOC);
    }


}