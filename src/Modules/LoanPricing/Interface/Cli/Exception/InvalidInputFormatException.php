<?php

declare(strict_types=1);

namespace FeeCalculator\Modules\LoanPricing\Interface\Cli\Exception;

use InvalidArgumentException;

final class InvalidInputFormatException extends InvalidArgumentException
{
    public static function fromValue(string $value): self
    {
        return new self("'{$value}' is not a valid numeric value");
    }
}
