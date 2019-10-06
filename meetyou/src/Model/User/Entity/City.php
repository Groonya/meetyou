<?php
declare(strict_types=1);

namespace App\Model\User\Entity;


use Webmozart\Assert\Assert;

class City
{
    /**
     * @var string
     */
    private $name;

    public function __construct(string $name)
    {
        Assert::notEmpty($name);
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }
}