<?php

declare(strict_types=1);

namespace FeeCalculator\Modules\LoanPricing\Interface\Cli\Service;

use FeeCalculator\Modules\LoanPricing\Interface\Cli\Exception\InvalidInputFormatException;
use NumberFormatter as IntlNumberFormatter;

final readonly class StringNumberParser
{
    private IntlNumberFormatter $formatter;

    public function __construct(string $locale)
    {
        $this->formatter = new IntlNumberFormatter($locale, IntlNumberFormatter::DECIMAL);
    }

    public function parse(string $value): float
    {
        $parsed = $this->formatter->parse($value);
        if ($parsed === false) {
            throw InvalidInputFormatException::fromValue($value);
        }

        return $parsed;
    }
}
