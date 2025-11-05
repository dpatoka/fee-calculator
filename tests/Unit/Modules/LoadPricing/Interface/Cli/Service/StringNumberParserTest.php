<?php

declare(strict_types=1);

namespace FeeCalculator\Tests\Unit\Modules\LoadPricing\Interface\Cli\Service;

use FeeCalculator\Modules\LoanPricing\Interface\Cli\Service\StringNumberParser;
use FeeCalculator\SharedKernel\Domain\Locale;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class StringNumberParserTest extends TestCase
{
    private StringNumberParser $parser;

    /**
     * @return array<string, array{string, float}>
     */
    public static function validFloatProvider(): array
    {
        return [
            'float with decimals' => ['123.45', 123.45],
            'negative float' => ['-99.99', -99.99],
            'zero value' => ['0', 0.0],
            'integer value' => ['100', 100.0],
            'negative integer' => ['-50', -50.0],
        ];
    }

    /**
     * @return array<string, array{string}>
     */
    public static function invalidFloatProvider(): array
    {
        return [
            'empty string' => ['', '\'\' is not a valid numeric value'],
            'non-numeric string' => ['invalid', '\'invalid\' is not a valid numeric value'],
            'currency format with pound sign' => ['£1,234.56', '\'£1,234.56\' is not a valid numeric value'],
        ];
    }

    protected function setUp(): void
    {
        $this->parser = new StringNumberParser(Locale::EN_GB->value);
    }

    #[DataProvider('validFloatProvider')]
    public function testParseWithValidInputs(string $input, float $expected): void
    {
        $result = $this->parser->parse($input);
        $this->assertSame($expected, $result);
    }

    #[DataProvider('invalidFloatProvider')]
    public function testParseThrowsExceptionForInvalidInputs(string $input, string $expectedMessage): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage($expectedMessage);

        $this->parser->parse($input);
    }
}
