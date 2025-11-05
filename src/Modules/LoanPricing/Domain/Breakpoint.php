<?php

declare(strict_types=1);

namespace FeeCalculator\Modules\LoanPricing\Domain;

final readonly class Breakpoint
{
    public function __construct(
        public float $amount,
        public float $fee,
    ) {
    }
}
