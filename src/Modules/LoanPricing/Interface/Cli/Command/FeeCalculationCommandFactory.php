<?php


declare(strict_types=1);

namespace FeeCalculator\Modules\LoanPricing\Interface\Cli\Command;

use FeeCalculator\Modules\LoanPricing\Application\FeeCalculationQueryHandler;
use FeeCalculator\Modules\LoanPricing\Infrastructure\Repository\InMemoryBreakpointRepository;
use FeeCalculator\Modules\LoanPricing\Interface\Cli\Service\CurrencyNumberFormatter;
use FeeCalculator\Modules\LoanPricing\Interface\Cli\Service\StringNumberParser;
use FeeCalculator\SharedKernel\Domain\Locale;

final readonly class FeeCalculationCommandFactory
{
    public static function create(): FeeCalculationCommand
    {
        return new FeeCalculationCommand(
            new FeeCalculationQueryHandler(
                new InMemoryBreakpointRepository()
            ),
            new StringNumberParser(Locale::EN_GB->value),
            new CurrencyNumberFormatter()
        );
    }
}
