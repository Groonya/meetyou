<?php
declare(strict_types=1);

namespace App\Model\User\Entity;


use RuntimeException;

class Gender
{
    /**
     * @var bool
     */
    private $value;

    private const MALE = 'male';
    private const FEMALE = 'female';

    public function __construct(string $value)
    {
        $value = strtolower($value);

        if (!in_array($value, [self::MALE, self::FEMALE], true)) {
            throw new RuntimeException("Unrecognized gender $value");
        }
        $this->value = $value;
    }

    public static function male(): Gender
    {
        return new self(self::MALE);
    }

    public static function female(): Gender
    {
        return new self(self::FEMALE);
    }

    public function isMale(): bool
    {
        return $this->value === self::MALE;
    }

    public function isFemale(): bool
    {
        return $this->value === self::FEMALE;
    }

    public function getValue(): string
    {
        return $this->value;
    }
}