<?php

declare(strict_types=1);

namespace FeeCalculator\Tests\Unit\Modules\LoadPricing\Infrastructure\Repository;

use FeeCalculator\Modules\LoanPricing\Domain\BreakpointRange;
use FeeCalculator\Modules\LoanPricing\Domain\Exception\AmountOutOfRangeException;
use FeeCalculator\Modules\LoanPricing\Domain\Exception\UnsupportedTermException;
use FeeCalculator\Modules\LoanPricing\Infrastructure\Repository\InMemoryBreakpointRepository;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Throwable;

final class InMemoryBreakpointRepositoryTest extends TestCase
{
    private InMemoryBreakpointRepository $repository;

    /**
     * @return array<string, array{
     *     term: int,
     *     requiredAmount: float,
     *     expectedLowerAmount: float,
     *     expectedLowerFee: float,
     *     expectedUpperAmount: float,
     *     expectedUpperFee: float
     * }>
     */
    public static function correctValuesProvider(): array
    {
        return [
            'term 12 amount 1000 exact match' => [
                'term' => 12,
                'requiredAmount' => 1000.0,
                'expectedLowerAmount' => 1000.0,
                'expectedLowerFee' => 50.0,
                'expectedUpperAmount' => 2000.0,
                'expectedUpperFee' => 90.0,
            ],
            'term 12 amount 1500 in first tier' => [
                'term' => 12,
                'requiredAmount' => 1500.0,
                'expectedLowerAmount' => 1000.0,
                'expectedLowerFee' => 50.0,
                'expectedUpperAmount' => 2000.0,
                'expectedUpperFee' => 90.0,
            ],
            'term 12 amount 20000 max amount' => [
                'term' => 12,
                'requiredAmount' => 20000.0,
                'expectedLowerAmount' => 20000.0,
                'expectedLowerFee' => 400.0,
                'expectedUpperAmount' => 20000.0,
                'expectedUpperFee' => 400.0,
            ],
            'term 24 amount 1000 exact match' => [
                'term' => 24,
                'requiredAmount' => 1000.0,
                'expectedLowerAmount' => 1000.0,
                'expectedLowerFee' => 70.0,
                'expectedUpperAmount' => 2000.0,
                'expectedUpperFee' => 100.0,
            ],
            'term 24 amount 20000 max amount' => [
                'term' => 24,
                'requiredAmount' => 20000.0,
                'expectedLowerAmount' => 20000.0,
                'expectedLowerFee' => 800.0,
                'expectedUpperAmount' => 20000.0,
                'expectedUpperFee' => 800.0,
            ],
        ];
    }

    /**
     * @return array<string, array{
     *     term: int,
     *     requiredAmount: float,
     *     expectedExceptionClass: class-string<\Throwable>,
     *     expectedExceptionMessage: string
     * }>
     */
    public static function incorrectValuesProvider(): array
    {
        return [
            'term 12 amount 0 below threshold' => [
                'term' => 12,
                'requiredAmount' => 0.0,
                'expectedExceptionClass' => AmountOutOfRangeException::class,
                'expectedExceptionMessage' => 'Requested amount 0 is below lower boundary 1000',
            ],
            'term 12 amount negative below threshold' => [
                'term' => 12,
                'requiredAmount' => -1.0,
                'expectedExceptionClass' => AmountOutOfRangeException::class,
                'expectedExceptionMessage' => 'Requested amount -1 is below lower boundary 1000',
            ],
            'term 12 amount 200 below threshold' => [
                'term' => 12,
                'requiredAmount' => 200.01,
                'expectedExceptionClass' => AmountOutOfRangeException::class,
                'expectedExceptionMessage' => 'Requested amount 200.01 is below lower boundary 1000',
            ],
            'term 6 unsupported term' => [
                'term' => 6,
                'requiredAmount' => 1000.0,
                'expectedExceptionClass' => UnsupportedTermException::class,
                'expectedExceptionMessage' => 'Term 6 not supported',
            ],
        ];
    }

    protected function setUp(): void
    {
        $this->repository = new InMemoryBreakpointRepository();
    }

    #[DataProvider('correctValuesProvider')]
    public function testGetForTermAndAmountWithCorrectValues(
        int $term,
        float $requiredAmount,
        float $expectedLowerAmount,
        float $expectedLowerFee,
        float $expectedUpperAmount,
        float $expectedUpperFee
    ): void {
        $breakpointRange = $this->repository->getForTermAndAmount($term, $requiredAmount);

        $this->assertInstanceOf(BreakpointRange::class, $breakpointRange);

        $this->assertEquals($expectedLowerAmount, $breakpointRange->lowerBreakpoint->amount);
        $this->assertEquals($expectedLowerFee, $breakpointRange->lowerBreakpoint->fee);

        $this->assertEquals($expectedUpperAmount, $breakpointRange->upperBreakpoint->amount);
        $this->assertEquals($expectedUpperFee, $breakpointRange->upperBreakpoint->fee);
    }

    #[DataProvider('incorrectValuesProvider')]
    public function testGetForTermAndAmountWithIncorrectValues(
        int $term,
        float $requiredAmount,
        string $expectedExceptionClass,
        string $expectedExceptionMessage
    ): void {
        /** @var class-string<Throwable> $expectedExceptionClass */
        $this->expectException($expectedExceptionClass);
        $this->expectExceptionMessage($expectedExceptionMessage);

        $this->repository->getForTermAndAmount($term, $requiredAmount);
    }
}
