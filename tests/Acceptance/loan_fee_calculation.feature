Feature: Loan Fee Calculation
  As a customer
  I want to know the fee for my loan
  So that I can choose the best lending option for me

  Scenario Outline: Calculates fee with success
    When I calculate fee for amount "<loan>" and term "<term>"
    Then the exit code should be 0
    And the output should contain "<fee>"
    Examples:
      | loan      | term | fee    | description                                           |
      | 19,250.00 | 12   | 385.00 | fee not rounded up, loan + fee already divisible by 5 |
      | 11,500.00 | 24   | 460.00 | fee not rounded up, loan + fee already divisible by 5 |
      | 1,123.00  | 12   | 57.00  | fee rounded up to make loan + fee divisible by 5      |
      | 2,567.00  | 24   | 113.00 | fee rounded up to make loan + fee divisible by 5      |

  Scenario Outline: Handles invalid input with error
    When I calculate fee for amount "<loan>" and term "<term>"
    Then the exit code should be 1
    And there should be an error containing "<error_message>"
    Examples:
      | loan     | term | error_message                      | description          |
      |          | 12   | '' is not a valid numeric value    | missing loan         |
      | abc      | 12   | 'abc' is not a valid numeric value | invalid loan value   |
      | 500.00   | 12   | below lower boundary               | amount below minimum |
      | -1000.00 | 12   | below lower boundary               | negative amount      |
      | 1000.00  |      | '' is not a valid numeric value    | missing term         |
      | 1000.00  | 36   | Term 36 not supported              | unsupported term     |
