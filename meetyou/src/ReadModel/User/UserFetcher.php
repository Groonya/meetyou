<?php
declare(strict_types=1);

namespace App\ReadModel\User;


use App\Mapper\User\UserMapper;
use App\Model\User\Entity\User;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\FetchMode;
use Knp\Component\Pager\Event\Subscriber\Paginate\Callback\CallbackPagination;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

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
        $sql = 'SELECT id, name_first, name_last FROM users ORDER BY name_first, name_last';

        $target = new CallbackPagination(function () {
            $stmt = $this->connection->prepare('SELECT COUNT(*) FROM users');
            $stmt->execute();

            return $stmt->fetchColumn();
        }, function (int $offset, int $limit) use ($sql) {
            $sql .= " LIMIT $offset, $limit";
            $stmt = $this->connection->prepare($sql);
            $stmt->execute();

            return $stmt->fetchAll(FetchMode::ASSOCIATIVE);
        });

        return $this->paginator->paginate($target, $page, $limit);
    }

    public function findById(string $id): ?User
    {
        return $this->mapper->findById($id);
    }
}