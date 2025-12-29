<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class CurrencyExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('currency_dt', [$this, 'formatCurrency']),
        ];
    }

    public function formatCurrency(float $amount, int $decimals = 2): string
    {
        return number_format($amount, $decimals) . ' DT';
    }
}
