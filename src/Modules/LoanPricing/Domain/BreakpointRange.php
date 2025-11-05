<?php

declare(strict_types=1);

namespace FeeCalculator\Modules\LoanPricing\Domain;

use FeeCalculator\Modules\LoanPricing\Domain\Exception\AmountOutOfRangeException;

final readonly class BreakpointRange
{
    public function __construct(
        public Breakpoint $lowerBreakpoint,
        public Breakpoint $upperBreakpoint,
    ) {
    }

    /**
     * @throws AmountOutOfRangeException
     */
    public function calculateFee(float $requestedAmount): float
    {
        $this->validateAmount($requestedAmount);

        if ($requestedAmount === $this->lowerBreakpoint->amount) {
            return $this->lowerBreakpoint->fee;
        }

        if ($requestedAmount === $this->upperBreakpoint->amount) {
            return $this->upperBreakpoint->fee;
        }

        $interpolatedFee = $this->interpolateFee($requestedAmount);

        return $this->roundUpToDivisibleByFive($requestedAmount, $interpolatedFee);
    }

    /**
     * @throws AmountOutOfRangeException
     */
    private function validateAmount(float $requestedAmount): void
    {
        if ($requestedAmount < $this->lowerBreakpoint->amount) {
            throw AmountOutOfRangeException::belowLowerBoundary($requestedAmount, $this->lowerBreakpoint->amount);
        }

        if ($requestedAmount > $this->upperBreakpoint->amount) {
            throw AmountOutOfRangeException::aboveUpperBoundary($requestedAmount, $this->upperBreakpoint->amount);
        }
    }

    private function interpolateFee(float $requestedAmount): float
    {
        $ratio = ($requestedAmount - $this->lowerBreakpoint->amount)
            / ($this->upperBreakpoint->amount - $this->lowerBreakpoint->amount);

        return $this->lowerBreakpoint->fee +
            ($this->upperBreakpoint->fee - $this->lowerBreakpoint->fee) * $ratio;
    }

    private function roundUpToDivisibleByFive(float $loanAmount, float $fee): float
    {
        $total = $loanAmount + $fee;
        $roundedTotal = ceil($total / 5) * 5;

        return $roundedTotal - $loanAmount;
    }
}
