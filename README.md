# Fee Calculator
## Table of Contents
- [Task](#task)
   - [Objective](#objective)
   - [Requirements](#requirements)
   - [Breakpoint Structure Example](#breakpoint-structure-example)
      - [Period 12](#period-12)
      - [Period 24](#period-24)
   - [Test Cases](#test-cases)
- [Solution](#solution)
   - [Run it](#run-it)
   - [Code standards](#code-standards)
   - [Architecture](#architecture)
   - [Calculation logic](#calculation-logic)
      - [Implemented](#implemented)
      - [Alternative](#alternative)
   - [Testing Strategy](#testing-strategy)

## Task
### Objective
Build a CLI-based fee calculator demonstrating OOP, SOLID principles, design patterns, and clean architecture.

### Requirements
**Business Rules:**
1. Non-formulaic Structure: The fee calculation doesn't follow a mathematical formula - it's based on discrete breakpoints
2. Linear Interpolation: For amounts between breakpoints, interpolate linearly between the lower and upper bounds
3. Rounding Rule: Round the fee UP to ensure (amount + fee) is exactly divisible by 5
4. Amount Constraints:
    - Minimum: 1,000
    - Maximum: 20,000
    - Precision: up to 2 decimal places
5. Duration Options: Only 12 or 24 units are valid
6. Flexible Structure: The system should support changing:
    - Number of breakpoints
    - Breakpoint values
    - Storage mechanism
7. Rate Variation: The same fee may apply to different amounts (non-linear progression)

**Input:**
- Amount (decimal, 2 places)
- Duration period (integer: 12 or 24 units)

**Output:**
- Calculated fee based on predefined breakpoints
- Fee must be rounded so total (amount + fee) is divisible by 5
- Format: decimal with 2 places (e.g., `1223.44`)

**Technical Constraints:**
- PHP 8+
- CLI implementation: `bin/calculate [amount] [period]`
- Exit code 0 on success, non-zero on failure
- Output to stdout, errors to stderr
- No web frameworks required

### Breakpoint Structure Example

#### Period 12

| Amount  | Fee |
|---------|-----|
| 1,000   | 50  |
| 2,000   | 90  |
| 3,000   | 90  |
| 4,000   | 115 |
| 5,000   | 100 |
| 6,000   | 120 |
| 7,000   | 140 |
| 8,000   | 160 |
| 9,000   | 180 |
| 10,000  | 200 |
| 11,000  | 220 |
| 12,000  | 240 |
| 13,000  | 260 |
| 14,000  | 280 |
| 15,000  | 300 |
| 16,000  | 320 |
| 17,000  | 340 |
| 18,000  | 360 |
| 19,000  | 380 |
| 20,000  | 400 |

#### Period 24

| Amount  | Fee |
|---------|-----|
| 1,000   | 70  |
| 2,000   | 100 |
| 3,000   | 120 |
| 4,000   | 160 |
| 5,000   | 200 |
| 6,000   | 240 |
| 7,000   | 280 |
| 8,000   | 320 |
| 9,000   | 360 |
| 10,000  | 400 |
| 11,000  | 440 |
| 12,000  | 480 |
| 13,000  | 520 |
| 14,000  | 560 |
| 15,000  | 600 |
| 16,000  | 640 |
| 17,000  | 680 |
| 18,000  | 720 |
| 19,000  | 760 |
| 20,000  | 800 |


### Test Cases

| Amount    | Period | Expected Fee |
|-----------|--------|--------------|
| 11,500.00 | 24     | 460.00       |
| 19,250.00 | 12     | 385.00       |

## Solution
### Run it
```console
make setup
make test
```

### Code standards
- `PSR12` and more, check `.dev/.php-cs-fixer.php`
- `PHPStan` level `10`

### Architecture
- I've done this project with a `Modular Monolith` approach.
   - It's a good start that opens a clear path into decomposition to `Microservices`.
   - Of course, decomposition should be done only when needed.
- I follow `Ports and Adapters`, `DDD` and `CQRS` (only query needed here).
- `SharedKernel` holds lightweight contracts that can be reused by all the Modules and keep the architecture consistent.
   - I've added them as `Marker interfaces`, but when necessary behaviors can be added to them.
      - Probably those needed by middleware which can be added later.
- When a Module needs to follow other architectural patterns, it's not blocked.
   - Not every Module needs `CQRS`, `DDD`, etc. Those are only tools to solve problems.
      - For example, patterns like `Pipe and filters` have also their place and can be used within `Modular Monolith`.
- Module entry point is the `Interface` layer.

### Calculation logic
#### Implemented
- `BreakpointRange` - contains calculation logic:
   - The class has a single, cohesive responsibility.
   - The calculations are tied to the concept of breakpoint range.
   - According to `DDD`, classes should be rich in behaviour and not anaemic data containers.
- There is no requirement for making different calculations strategies possible.
   - When needed, the algorithm can be easily extracted and encapsulated with `Strategy Pattern`.
   - Now it's not needed, so I'm following `YAGNI` and `KISS`.
      - Not overengineering is a virtue for me.
   - It will be easy to evolve the solution:
      - I prefer `Emerging Architecture` and iterative problem-solving
      - Those can be done thanks to `SOLID` code and good tests.
         - Code should be flexible as clay and not be brittle as a stone.


#### Alternative

- Lots of problems can be solved with usage of `linked lists`.
- Here, it could be used together with the `Chain of Responsibility` pattern.
- I haven't implemented it as it's not practical for this task:
   - Has a time complexity of O(n) in the worst case.
   - On the contrary, `Symfony Voters` are a good use usage of altered `Chain of Responsibility` pattern.
- Pseudocode to show the idea for calculator:

```php
final readonly class Breakpoint
{
    public function __construct(
        private int $term,
        private float $amount,
        private float $fee,
        private Breakpoint $nextBreakpoint
    ) {
    }
    
    public function handle(int $term, float $amount): float
    {
         # check term and amount, lookup values of nextBreakpoint for range
        if ($this->canHandle($term, $amount) === false) {
            return $this->nextBreakpoint->handle($term, $amount);
        }
        
        # use local values and lookup values of nextBreakpoint for interpolation
        return $this->calculateFee($amount);
    }
```


### Testing Strategy
- For me tests are a design tool, living documentation and safety net (regression testing). I do `TDD` and `BDD` 🙂.
   - They help me to split a problem into smaller ones and resolve them one by one.
- I like the [Detroit TDD school](https://zone84.tech/architecture/london-and-detroit-schools-of-unit-tests/) (Kent Beck's) so:
   - Unit under tests is not the method or the class. It's the feature with the stable interface.
   - My `unit tests` are [sociable](https://martinfowler.com/bliki/UnitTest.html).
   - I use mocks for cutting off:
      - heavy dependencies - to be able to test only part of the huge process,
      - outer-world dependencies - things I have no control.
   - Given all of that, here I don't use mocks.
- I use `Behat tests` as acceptance scenarios.
   - I keep them at a high level to encapsulate implementation details, also on business rules level.
   - They allow testing the whole app flow from top to down.
   - I rely on them instead of writing separate tests for each class created
      - Why?
         - Kent Beck - the author of TDD - said:
           > I get paid for code that works, not for tests
         - Writing, maintaining and executing tests costs time and money.
         - It's crucial to choose what and how to test wisely. My goal is to get the most bang for my buck.
         - However, if the code is crucial and bugs cost a lot - it should be fully tested. The cost of tests will pay off.
- The Outcomes of such an approach are:
   - Tests check business behavior over implementation.
   - Tests are more stable against refactoring so encourage continuous improvements.
   - Tests are high quality code which documents and explains the behaviour of code.
   - In result: tests help to ship new features faster.