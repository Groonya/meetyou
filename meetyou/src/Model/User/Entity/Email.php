<?php
declare(strict_types=1);

namespace App\Model\User\Entity;


use Webmozart\Assert\Assert;

class Email
{
    /**
     * @var string
     */
    private $value;

    public function __construct(string $value)
    {
        Assert::email($value);

        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }
}