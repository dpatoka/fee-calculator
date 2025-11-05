<?php

declare(strict_types=1);

namespace FeeCalculator\Tests\Acceptance;

use Behat\Behat\Context\Context;
use PHPUnit\Framework\Assert;
use Symfony\Component\Process\Process;

final class AcceptanceContext implements Context
{
    private Process $process;

    private string $output;

    private string $errorOutput;

    /**
     * @When I calculate fee for amount :amount and term :term
     */
    public function iCalculateFeeForAmountAndTerm(string $amount, string $term): void
    {
        $command = sprintf('php bin/calculate "%s" "%s"', $amount, $term);
        $this->process = Process::fromShellCommandline($command);
        $this->process->run();
        $this->output = $this->process->getOutput();
        $this->errorOutput = $this->process->getErrorOutput();
    }

    /**
     * @Then the output should contain :text
     */
    public function theOutputShouldContain(string $text): void
    {
        $cleanOutput = trim($this->output);

        Assert::assertEquals($text, $cleanOutput);
    }

    /**
     * @Then the exit code should be :code
     */
    public function theExitCodeShouldBe(string $code): void
    {
        Assert::assertEquals((int) $code, $this->process->getExitCode());
    }

    /**
     * @Then there should be an error containing :text
     */
    public function thereShouldBeAnErrorContaining(string $text): void
    {
        Assert::assertStringContainsString($text, $this->errorOutput);
    }
}
