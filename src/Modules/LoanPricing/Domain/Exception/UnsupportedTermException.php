<?php

declare(strict_types=1);

namespace FeeCalculator\Modules\LoanPricing\Domain\Exception;

final class UnsupportedTermException extends LoanPricingDomainException
{
    public static function forTerm(int $term): self
    {
        return new self("Term {$term} not supported");
    }

    private function __construct(string $message)
    {
        parent::__construct($message);
    }
}
