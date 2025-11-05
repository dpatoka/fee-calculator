<?php

declare(strict_types=1);

namespace FeeCalculator\Modules\LoanPricing\Domain;

use FeeCalculator\Modules\LoanPricing\Domain\Exception\AmountOutOfRangeException;
use FeeCalculator\Modules\LoanPricing\Domain\Exception\NoBreakpointsAvailableException;
use FeeCalculator\Modules\LoanPricing\Domain\Exception\NoLowerBreakpointFoundException;
use FeeCalculator\Modules\LoanPricing\Domain\Exception\UnsupportedTermException;

interface BreakpointRepository
{
    /**
     * @throws UnsupportedTermException
     * @throws NoBreakpointsAvailableException
     * @throws AmountOutOfRangeException
     * @throws NoLowerBreakpointFoundException
     */
    public function getForTermAndAmount(int $term, float $requiredAmount): BreakpointRange;
}
