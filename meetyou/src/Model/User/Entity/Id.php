<?php
declare(strict_types=1);

namespace App\Model\User\Entity;


use Ramsey\Uuid\Uuid;

class Id
{
    /**
     * @var string
     */
    private $id;

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    public static function next(): self
    {
        return new self(Uuid::uuid4()->toString());
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }
}