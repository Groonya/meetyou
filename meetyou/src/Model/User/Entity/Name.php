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
        $this->first = mb_strtolower($first);
        $this->last = mb_strtolower($last);
    }

    /**
     * @return string
     */
    public function getFirst(): string
    {
        return $this->capitalizeFirstChar($this->first);
    }

    /**
     * @return string
     */
    public function getLast(): string
    {
        return $this->capitalizeFirstChar($this->last);
    }

    public function full(): string
    {
        $first = $this->getFirst();
        $last = $this->getLast();

        return "{$first} $last";
    }

    private function capitalizeFirstChar(string $str): string
    {
        $first = mb_substr($str, 0, 1);
        $other = mb_substr($str, 1);

        return mb_strtoupper($first).$other;
    }
}