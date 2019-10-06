<?php
declare(strict_types=1);

namespace App\Model\User\Entity;


use Webmozart\Assert\Assert;

class Name
{
    /**
     * @var string
     */
    private $first;
    /**
     * @var string
     */
    private $last;

    public function __construct(string $first, string $last)
    {
        Assert::notEmpty($first);
        Assert::notEmpty($last);
        $this->first = $first;
        $this->last = $last;
    }

    /**
     * @return string
     */
    public function getFirst(): string
    {
        return $this->first;
    }

    /**
     * @return string
     */
    public function getLast(): string
    {
        return $this->last;
    }
}