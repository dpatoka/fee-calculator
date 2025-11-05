<?php

declare(strict_types=1);

namespace FeeCalculator\Modules\LoanPricing\Interface\Cli\Service;

final readonly class CurrencyNumberFormatter
{
    public function format(float $value): string
    {
        return number_format($value, 2);
    }
}
