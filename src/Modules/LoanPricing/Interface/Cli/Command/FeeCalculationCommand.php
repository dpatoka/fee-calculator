<?php

declare(strict_types=1);

namespace FeeCalculator\Modules\LoanPricing\Interface\Cli\Command;

use FeeCalculator\Modules\LoanPricing\Application\FeeCalculationQuery;
use FeeCalculator\Modules\LoanPricing\Application\FeeCalculationQueryHandler;
use FeeCalculator\Modules\LoanPricing\Domain\Exception\AmountOutOfRangeException;
use FeeCalculator\Modules\LoanPricing\Domain\Exception\NoBreakpointsAvailableException;
use FeeCalculator\Modules\LoanPricing\Domain\Exception\NoLowerBreakpointFoundException;
use FeeCalculator\Modules\LoanPricing\Domain\Exception\UnsupportedTermException;
use FeeCalculator\Modules\LoanPricing\Interface\Cli\Service\CurrencyNumberFormatter;
use FeeCalculator\Modules\LoanPricing\Interface\Cli\Service\StringNumberParser;
use FeeCalculator\SharedKernel\Interface\Cli\ConsoleCommand;

final readonly class FeeCalculationCommand implements ConsoleCommand
{
    public function __construct(
        private FeeCalculationQueryHandler $queryHandler,
        private StringNumberParser $numberParser,
        private CurrencyNumberFormatter $numberFormatter
    ) {
    }

    /**
     * @throws AmountOutOfRangeException
     * @throws NoBreakpointsAvailableException
     * @throws NoLowerBreakpointFoundException
     * @throws UnsupportedTermException
     */
    public function execute(string $amount, string $term): string
    {
        $request = $this->createQuery($amount, $term);
        $fee = $this->queryHandler->run($request);

        return $this->numberFormatter->format($fee);
    }

    private function createQuery(string $amount, string $term): FeeCalculationQuery
    {
        $parsedAmount = $this->numberParser->parse($amount);
        $parsedTerm = $this->numberParser->parse($term);

        return new FeeCalculationQuery(
            $parsedAmount,
            (int) $parsedTerm
        );
    }
}
