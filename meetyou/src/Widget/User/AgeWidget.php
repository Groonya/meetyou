<?php
declare(strict_types=1);

namespace App\Widget\User;


use DateTimeImmutable;
use DateTimeInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AgeWidget extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('age', [$this, 'age']),
        ];
    }

    public function age(DateTimeInterface $date): int
    {
        return $date->diff(new DateTimeImmutable())->y;
    }

}