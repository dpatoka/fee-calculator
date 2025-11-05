<?php

declare(strict_types=1);

namespace FeeCalculator\Modules\LoanPricing\Domain\Exception;

final class AmountOutOfRangeException extends LoanPricingDomainException
{
    public static function belowLowerBoundary(float $requestedAmount, float $lowerAmount): self
    {
        return new self("Requested amount {$requestedAmount} is below lower boundary {$lowerAmount}");
    }

    public static function aboveUpperBoundary(float $requestedAmount, float $upperAmount): self
    {
        return new self("Requested amount {$requestedAmount} is above upper boundary {$upperAmount}");
    }
}
