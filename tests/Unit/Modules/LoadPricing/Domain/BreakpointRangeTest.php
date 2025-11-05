<?php

declare(strict_types=1);

namespace FeeCalculator\Tests\Unit\Modules\LoadPricing\Domain;

use FeeCalculator\Modules\LoanPricing\Domain\Breakpoint;
use FeeCalculator\Modules\LoanPricing\Domain\BreakpointRange;
use FeeCalculator\Modules\LoanPricing\Domain\Exception\AmountOutOfRangeException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Throwable;

class BreakpointRangeTest extends TestCase
{
    /**
     * @return array<string, array<string, mixed>>
     */
    public static function boundaryTestProvider(): array
    {
        return [
            'at lower breakpoint term 12' => [
                'lowerAmount' => 1000,
                'lowerFee' => 50,
                'upperAmount' => 2000,
                'upperFee' => 90,
                'requestedAmount' => 1000,
                'expectedFee' => 50
            ],
            'at upper breakpoint term 12' => [
                'lowerAmount' => 1000,
                'lowerFee' => 50,
                'upperAmount' => 2000,
                'upperFee' => 90,
                'requestedAmount' => 2000,
                'expectedFee' => 90
            ],
        ];
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public static function interpolationNoRoundingProvider(): array
    {
        return [
            'midpoint interpolation' => [
                'lowerAmount' => 1000,
                'lowerFee' => 50,
                'upperAmount' => 2000,
                'upperFee' => 90,
                'requestedAmount' => 1500,
                'expectedFee' => 70
            ],
            'quarter point interpolation' => [
                'lowerAmount' => 1000,
                'lowerFee' => 50,
                'upperAmount' => 2000,
                'upperFee' => 90,
                'requestedAmount' => 1250,
                'expectedFee' => 60
            ],
            'equal fees at both breakpoints' => [
                'lowerAmount' => 2000,
                'lowerFee' => 90,
                'upperAmount' => 3000,
                'upperFee' => 90,
                'requestedAmount' => 2500,
                'expectedFee' => 90
            ],
            'midpoint interpolation with large amounts' => [
                'lowerAmount' => 15000,
                'lowerFee' => 600,
                'upperAmount' => 20000,
                'upperFee' => 800,
                'requestedAmount' => 17500,
                'expectedFee' => 700
            ],
        ];
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public static function interpolationWithRoundingProvider(): array
    {
        return [
            'basic rounding' => [
                'lowerAmount' => 1000,
                'lowerFee' => 50,
                'upperAmount' => 2000,
                'upperFee' => 90,
                'requestedAmount' => 1300,
                'expectedFee' => 65
            ],
            'edge case: decreasing fee rounding where upper fee < lower fee' => [
                'lowerAmount' => 4000,
                'lowerFee' => 115,
                'upperAmount' => 5000,
                'upperFee' => 100,
                'requestedAmount' => 4500,
                'expectedFee' => 110
            ],
        ];
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public static function outOfRangeProvider(): array
    {
        return [
            'below lower breakpoint term 12' => [
                'lowerAmount' => 1000,
                'lowerFee' => 50,
                'upperAmount' => 2000,
                'upperFee' => 90,
                'requestedAmount' => 500,
                'expectedException' => AmountOutOfRangeException::class
            ],
            'above upper breakpoint term 12' => [
                'lowerAmount' => 1000,
                'lowerFee' => 50,
                'upperAmount' => 2000,
                'upperFee' => 90,
                'requestedAmount' => 2500,
                'expectedException' => AmountOutOfRangeException::class
            ],
        ];
    }

    #[DataProvider('boundaryTestProvider')]
    public function testCalculateFeeAtBoundaries(
        float $lowerAmount,
        float $lowerFee,
        float $upperAmount,
        float $upperFee,
        float $requestedAmount,
        float $expectedFee
    ): void {
        $fee = $this->calculate($lowerAmount, $lowerFee, $upperAmount, $upperFee, $requestedAmount);

        $this->assertEquals($expectedFee, $fee);
    }

    #[DataProvider('interpolationNoRoundingProvider')]
    public function testInterpolationWithoutRounding(
        float $lowerAmount,
        float $lowerFee,
        float $upperAmount,
        float $upperFee,
        float $requestedAmount,
        float $expectedFee
    ): void {
        $fee = $this->calculate($lowerAmount, $lowerFee, $upperAmount, $upperFee, $requestedAmount);

        $this->assertEquals($expectedFee, $fee);
    }

    #[DataProvider('interpolationWithRoundingProvider')]
    public function testInterpolationWithRounding(
        float $lowerAmount,
        float $lowerFee,
        float $upperAmount,
        float $upperFee,
        float $requestedAmount,
        float $expectedFee
    ): void {
        $fee = $this->calculate($lowerAmount, $lowerFee, $upperAmount, $upperFee, $requestedAmount);

        $this->assertEquals($expectedFee, $fee);
    }

    /**
     * @param class-string<Throwable> $expectedException
     */
    #[DataProvider('outOfRangeProvider')]
    public function testCalculateFeeThrowsExceptionForOutOfRangeAmounts(
        float $lowerAmount,
        float $lowerFee,
        float $upperAmount,
        float $upperFee,
        float $requestedAmount,
        string $expectedException
    ): void {
        $this->expectException($expectedException);

        $this->calculate($lowerAmount, $lowerFee, $upperAmount, $upperFee, $requestedAmount);
    }

    private function calculate(float $lowerAmount, float $lowerFee, float $upperAmount, float $upperFee, float $requestedAmount): float
    {
        $lowerBreakpoint = new Breakpoint($lowerAmount, $lowerFee);
        $upperBreakpoint = new Breakpoint($upperAmount, $upperFee);
        $range = new BreakpointRange($lowerBreakpoint, $upperBreakpoint);

        return $range->calculateFee($requestedAmount);
    }
}
