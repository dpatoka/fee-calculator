<?php

declare(strict_types=1);

namespace FeeCalculator\Modules\LoanPricing\Application;

use FeeCalculator\SharedKernel\Application\Command;

final readonly class FeeCalculationQuery implements Command
{
    public function __construct(
        public float $amount,
        public int $term
    ) {
    }
}
