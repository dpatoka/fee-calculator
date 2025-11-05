<?php

declare(strict_types=1);

namespace FeeCalculator\Modules\LoanPricing\Infrastructure\Repository;

use FeeCalculator\Modules\LoanPricing\Domain\Breakpoint;
use FeeCalculator\Modules\LoanPricing\Domain\BreakpointRange;
use FeeCalculator\Modules\LoanPricing\Domain\BreakpointRepository;
use FeeCalculator\Modules\LoanPricing\Domain\Exception\AmountOutOfRangeException;
use FeeCalculator\Modules\LoanPricing\Domain\Exception\NoBreakpointsAvailableException;
use FeeCalculator\Modules\LoanPricing\Domain\Exception\NoLowerBreakpointFoundException;
use FeeCalculator\Modules\LoanPricing\Domain\Exception\UnsupportedTermException;

/**
 * This is a naive implementation for task purposes.
 * Real-life scenario will fetch data from some storage.
 * When storage will change, another implementation of BreakpointRepository should be done to meet storage best practices.
 */
final readonly class InMemoryBreakpointRepository implements BreakpointRepository
{
    /**
     * @var array<int, array<float, float>>
     */
    private const array BREAKPOINTS = [
        12 => [
            1000.0 => 50.0,
            2000.0 => 90.0,
            3000.0 => 90.0,
            4000.0 => 115.0,
            5000.0 => 100.0,
            6000.0 => 120.0,
            7000.0 => 140.0,
            8000.0 => 160.0,
            9000.0 => 180.0,
            10000.0 => 200.0,
            11000.0 => 220.0,
            12000.0 => 240.0,
            13000.0 => 260.0,
            14000.0 => 280.0,
            15000.0 => 300.0,
            16000.0 => 320.0,
            17000.0 => 340.0,
            18000.0 => 360.0,
            19000.0 => 380.0,
            20000.0 => 400.0,
        ],
        24 => [
            1000.0 => 70.0,
            2000.0 => 100.0,
            3000.0 => 120.0,
            4000.0 => 160.0,
            5000.0 => 200.0,
            6000.0 => 240.0,
            7000.0 => 280.0,
            8000.0 => 320.0,
            9000.0 => 360.0,
            10000.0 => 400.0,
            11000.0 => 440.0,
            12000.0 => 480.0,
            13000.0 => 520.0,
            14000.0 => 560.0,
            15000.0 => 600.0,
            16000.0 => 640.0,
            17000.0 => 680.0,
            18000.0 => 720.0,
            19000.0 => 760.0,
            20000.0 => 800.0,
        ],
    ];

    public function getForTermAndAmount(int $term, float $requiredAmount): BreakpointRange
    {
        $breakpoints = $this->getTermBreakpoints($term);
        $this->validateAmountBounds($requiredAmount, $breakpoints);

        $lowerBreakpoint = $this->findLowerBreakpoint($requiredAmount, $breakpoints);
        $upperBreakpoint = $this->findUpperBreakpoint($requiredAmount, $breakpoints);

        return new BreakpointRange($lowerBreakpoint, $upperBreakpoint);
    }

    /**
     * @throws UnsupportedTermException
     * @return array<float, float>
     */
    private function getTermBreakpoints(int $term): array
    {
        if (!isset(self::BREAKPOINTS[$term])) {
            throw UnsupportedTermException::forTerm($term);
        }

        return self::BREAKPOINTS[$term];
    }

    /**
     * @param array<float, float> $breakpoints
     * @throws AmountOutOfRangeException
     * @throws NoBreakpointsAvailableException
     */
    private function validateAmountBounds(float $requiredAmount, array $breakpoints): void
    {
        if (empty($breakpoints)) {
            throw NoBreakpointsAvailableException::forAmount($requiredAmount);
        }

        $allAmounts = array_keys($breakpoints);
        $minAmount = min($allAmounts);
        $maxAmount = max($allAmounts);

        if ($requiredAmount < $minAmount) {
            throw AmountOutOfRangeException::belowLowerBoundary($requiredAmount, $minAmount);
        }

        if ($requiredAmount > $maxAmount) {
            throw AmountOutOfRangeException::aboveUpperBoundary($requiredAmount, $maxAmount);
        }
    }

    /**
     * @param array<float, float> $breakpoints
     * @throws NoLowerBreakpointFoundException
     */
    private function findLowerBreakpoint(float $requiredAmount, array $breakpoints): Breakpoint
    {
        $validAmounts = array_filter(
            array_keys($breakpoints),
            static fn ($amount) => $requiredAmount >= $amount
        );

        if (empty($validAmounts)) {
            throw NoLowerBreakpointFoundException::forAmount($requiredAmount);
        }

        $lowerAmount = max($validAmounts);
        return new Breakpoint($lowerAmount, $breakpoints[$lowerAmount]);
    }

    /**
     * @param array<float, float> $breakpoints
     */
    private function findUpperBreakpoint(float $requiredAmount, array $breakpoints): Breakpoint
    {
        $validAmounts = array_filter(
            array_keys($breakpoints),
            static fn ($amount) => $requiredAmount < $amount
        );

        if (empty($validAmounts)) {
            $allAmounts = array_keys($breakpoints);
            assert(!empty($allAmounts));
            $upperAmount = max($allAmounts);
            return new Breakpoint($upperAmount, $breakpoints[$upperAmount]);
        }

        $upperAmount = min($validAmounts);
        return new Breakpoint($upperAmount, $breakpoints[$upperAmount]);
    }
}
