<?php

declare(strict_types=1);

namespace FeeCalculator\Tests\Unit\Modules\LoadPricing\Interface\Cli\Service;

use FeeCalculator\Modules\LoanPricing\Interface\Cli\Service\CurrencyNumberFormatter;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class CurrencyNumberFormatterTest extends TestCase
{
    private CurrencyNumberFormatter $formatter;

    /**
     * @return array<string, array{float, string}>
     */
    public static function formatDataProvider(): array
    {
        return [
            'zero' => [0.0, '0.00'],
            'simple integer' => [100.0, '100.00'],
            'simple decimal' => [123.45, '123.45'],
            'single decimal place' => [99.9, '99.90'],
            'thousands separator' => [1234.56, '1,234.56'],
            'millions' => [12000000.0, '12,000,000.00'],
            'rounding up' => [99.999, '100.00'],
            'negative single decimal place' => [-100.0, '-100.00'],
            'negative with decimals' => [-123.45, '-123.45'],
            'negative with thousands' => [-1234.56, '-1,234.56'],
            'very small positive' => [0.001, '0.00'],
            'very small negative' => [-0.001, '0.00'],
            'edge case half cent up' => [0.015, '0.02'],
        ];
    }

    protected function setUp(): void
    {
        $this->formatter = new CurrencyNumberFormatter();
    }

    #[DataProvider('formatDataProvider')]
    public function testFormat(float $input, string $expected): void
    {
        $result = $this->formatter->format($input);

        $this->assertSame($expected, $result);
    }
}
