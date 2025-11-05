<?php

declare(strict_types=1);

namespace FeeCalculator\Modules\LoanPricing\Domain\Exception;

final class NoLowerBreakpointFoundException extends LoanPricingDomainException
{
    public static function forAmount(float $amount): self
    {
        return new self("No lower breakpoint found for amount {$amount}");
    }

    private function __construct(string $message)
    {
        parent::__construct($message);
    }
}
