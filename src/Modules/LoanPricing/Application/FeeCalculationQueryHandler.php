<?php

declare(strict_types=1);

namespace FeeCalculator\Modules\LoanPricing\Application;

use FeeCalculator\Modules\LoanPricing\Domain\BreakpointRepository;
use FeeCalculator\Modules\LoanPricing\Domain\Exception\AmountOutOfRangeException;
use FeeCalculator\Modules\LoanPricing\Domain\Exception\NoBreakpointsAvailableException;
use FeeCalculator\Modules\LoanPricing\Domain\Exception\NoLowerBreakpointFoundException;
use FeeCalculator\Modules\LoanPricing\Domain\Exception\UnsupportedTermException;
use FeeCalculator\SharedKernel\Application\QueryHandler;

final readonly class FeeCalculationQueryHandler implements QueryHandler
{
    public function __construct(
        private BreakpointRepository $breakpointRepository
    ) {
    }

    /**
     * @throws AmountOutOfRangeException
     * @throws NoBreakpointsAvailableException
     * @throws NoLowerBreakpointFoundException
     * @throws UnsupportedTermException
     */
    public function run(FeeCalculationQuery $query): float
    {
        $breakpoint = $this->breakpointRepository->getForTermAndAmount($query->term, $query->amount);

        return $breakpoint->calculateFee($query->amount);
    }
}
