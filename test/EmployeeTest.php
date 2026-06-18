<?php

/**
 * Unit tests for the Employee class
 * @author  Arturo Mora-Rioja
 * @version 1.0.0, September 2022
 * @version 1.1.0, June 2026 More comprehensive black-box design applied
 *                           Comments added 
 */

require_once 'classes/employee.php';

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class EmployeeTest extends TestCase {
    private Employee $employee;

    //   First and last name                Middle value  Boundary values
    //   ---------------------------------- ------------- --------------------------------------------------------
    //   Invalid partition: empty           empty         empty | 1 character 
    //   Valid partition: 1-30 characters   15 characters empty | 1 character | 2 characters
    //                                                    29 characters | 30 characters | 31 characters 
    //   Invalid partition: > 30 characters 45 characters 30 characters | 31 characters | 32 characters

    private const STRING_30_CHARS = [

        // Positive tests
        ['', false],                                // 0 characters
        ['A', true],                                // 1 character
        ['AB', true],                               // 2 characters
        ['ABCDEFGHIJKLMNO', true],                  // 15 characters
        ['abcdefghijklmno', true],                  // 15 characters
        ['ABCDEFGHIJKLMNOPQRSTUVWXYZABC', true],    // 29 characters
        ['ABCDEFGHIJKLMNOPQRSTUVWXYZABCD', true],   // 30 characters
        // Format 
        ['æøåñç', true],                            // AMR: I found this out thanks to this unit test!
        ['áéíóúàèìòùäëïöü', true],                  // AMR: I found this out thanks to this unit test!
        ['âêîôû', true],                            // AMR: I found this out thanks to this unit test!
        ['ÆØÅÑÇ', true],                            // AMR: I found this out thanks to this unit test!
        ['ÁÉÍÓÚÀÈÌÒÙÄËÏÖÜ', true],                  // AMR: I found this out thanks to this unit test!
        ['ÂÊÎÔÛ', true],                            // AMR: I found this out thanks to this unit test!

        // Negative tests
        ['ABCDEFGHIJKLMNOPQRSTUVWXYZABCDE', false], // 31 characters
        ['ABCDEFGHIJKLMNOPQRSTUVWXYZABCDF', false], // 32 characters
        ['ABCDEFGHIJKLMNOPQRSTUVWXYZABCDEFGHIJKLMNOPQRS', false],   // 45 characters
        ['abcdef1', false],                   
        ['abcdef/', false],                   
        ['abcdef,', false],                   
        // The following cases are unlikely to be valid, but they are according to requirements.
        // In a real company scenario, the person(s) in charge of writing requirements should be contacted to clarify the situation
        ['a a a a a a a', true],                   
        ['a-a-a-a-a-a-a', true],                   
        ['-', true],                                
        [' ', true],                                
    ];

    public function setUp(): void 
    {
        $this->employee = new Employee;
    }

    public function tearDown(): void 
    {
        unset($this->employee);
    }

    //   CPR                                Middle value  Boundary values
    //   ---------------------------------- ------------- --------------------------------------------------------
    //   Invalid partition: empty           empty         empty | 1 character 
    //   Invalid partition: 1-9 characters  5 characters  empty | 1 character | 2 characters
    //                                                    8 characters | 9 characters | 10 characters
    //   Valid partition:   10 characters   10 characters 9 characters | 10 characters | 11 characters
    //   Invalid partition: > 10 characters 15 characters 10 characters | 11 characters | 12 characters

    #[DataProvider('provideCPR')]
    public function testCpr($value, $expected): void 
    {
        $res = $this->employee->setCpr($value);

        $this->assertEquals($expected, $res);
    }
    public static function provideCpr(): array 
    {
        return [
            // Positive tests
            ['1234567890', true],   // 10 characters
            ['0000000000', true],
            ['9999999999', true],
            [1234567890, true],     // PHP automatically converts a 10-digit int to a string

            // Negative tests
            ['1', false],               // 1 character
            ['12', false],              // 2 characters
            ['12345', false],           // 5 characters
            ['12345678', false],        // 8 characters
            ['123456789', false],       // 9 characters
            ['12345678901', false],     // 11 characters
            ['123456789012', false],    // 12 characters
            ['123456789012345', false], // 15 characters
            ['ABCDEFGHIJ', false],      // Format / Edge case
            ['          ', false],      // Format / Edge case
            [true, false],              // Format / Edge case
            ['', false],                // Format / Edge case
            // [null, false],           // TypeError
            // [[9, 9, 9, 9, 9, 9, 9, 9, 9, 9], false], // TypeError
        ];
    }

    #[DataProvider('provideFirstName')]
    public function testFirstName($value, $expected): void 
    {
        $res = $this->employee->setFirstName($value);

        $this->assertEquals($expected, $res);
    }
    public static function provideFirstName(): array 
    {
        return self::STRING_30_CHARS;
    }

    //   There are so few department values that testing all of them is not costly

    #[DataProvider('provideDepartment')]
    public function testDepartment($value, $expected): void 
    {
        $res = $this->employee->setDepartment($value);

        $this->assertEquals($expected, $res);
    }
    public static function provideDepartment(): array 
    {
        return [
            // Positive tests
            ['HR', true],
            ['Finance', true],
            ['IT', true],
            ['Sales', true],
            ['General Services', true],

            // Negative tests
            ['Bonds', false],
            ['', false],
            [0, false],
        ];
    }

    //   Base salary                                Middle value Boundary values
    //   ------------------------------------------ ------------ --------------------------------------------------------
    //   Invalid partition: -MAX FLOAT- -0.01          -10000 kr  -MAX FLOAT - 0.01 | -MAX FLOAT | -MAX FLOAT + 0.01
    //                                                            -0.02 | -0.01 | 0
    //   Invalid partition: 0                               0     -0.01 | 0 | 0.01
    //   Invalid partition: 0.01-19999.99 kr            10000 kr  0 | 0.01 | 0.02
    //                                                            19999.98 | 19999.99 | 20000
    //   Valid partition: 20000-100000 kr               60000 kr  19999.99 | 20000 | 20000.01
    //                                                            999999.99 | 100000 | 100000.01
    //   Invalid partition: 100000.01-MAX FLOAT kr     120000 kr  100000 | 100000.01 | 100000.02
    //                                                            MAX FLOAT - 0.01 | MAX FLOAT | MAX FLOAT + 0.01

    #[DataProvider('provideBaseSalary')]
    public function testBaseSalary($value, $expected): void 
    {
        $res = $this->employee->setBaseSalary($value);

        $this->assertEquals($expected, $res);
    }
    public static function provideBaseSalary(): array 
    {
        return [
            // Positive tests
            [20000, true],          // Valid lower boundary
            [20000.01, true],       // Valid lower boundary + 1 (3-value approach)
            [60000, true],          // Middle value for the valid input partition
            [100000, true],         // Valid upper boundary
            [99999.99, true],       // Valid upper boundary - 1 (3-value approach)

            // Negative tests
            [-PHP_FLOAT_MAX - 0.01, false],
            [-PHP_FLOAT_MAX, false],
            [-PHP_FLOAT_MAX + 0.01, false],
            [-10000, false],         
            [-0.02, false],
            [-0.01, false],
            [0, false],
            [0.01, false],
            [0.02, false],
            [10000, false],         
            [100000.01, false],     
            [100000.02, false],     
            [19999.98, false],      
            [19999.99, false],      
            [120000, false],        
            [PHP_FLOAT_MAX - 0.01, false],
            [PHP_FLOAT_MAX, false],
            [PHP_FLOAT_MAX + 0.01, false],
            [false, false],
        ];
    }

    //   There are so few values that testing all of them is not costly

    #[DataProvider('provideEducationalLevel')]
    public function testEducationalLevel($value, $expected): void 
    {
        $res = $this->employee->setEducationalLevel($value);

        $this->assertEquals($expected, $res);
    }
    public static function provideEducationalLevel(): array
    {
        return [
            // Positive tests
            [0, true],          
            [1, true],          
            [2, true],          
            [3, true],       
            
            // Negative tests
            [-1, false],
            [4, false],
            [10, false],
            [-10, false],
        ];
    }

    //   Testing non-deterministic data is complicated. Here the date of birth must be compared to the present date, which changes daily.
    //   One approach is to calculate several dates relative to today 
    //   (in the past, in the future, 18 years ago, right before 18 years ago, right after 18 years ago).
    //   Unfortunately, it enforces an anti-pattern: calculations taking place in a unit test

    #[DataProvider('provideDateOfBirth')]
    public function testDateOfBirth($value, $expected): void 
    {
        $res = $this->employee->setDateOfBirth($value);

        $this->assertEquals($expected, $res);
    }
    public static function provideDateOfBirth(): array
    {
        $today = new DateTimeImmutable();
        $format = 'd/m/Y';

        // Base date: 18 years ago from today
        $eighteenYearsAgo = $today->modify('-18 years');

        return [
            [$eighteenYearsAgo->format($format), true],
            [$eighteenYearsAgo->modify('+1 day')->format($format), false],
            [$eighteenYearsAgo->modify('-1 day')->format($format), true],
            [$eighteenYearsAgo->modify('-10 days')->format($format), true],
            [$eighteenYearsAgo->modify('+10 days')->format($format), false],
            [$eighteenYearsAgo->modify('-8 years')->format($format), true],
            [$eighteenYearsAgo->modify('+8 years')->format($format), false],
            ['', false],
            ['31/02/1970', false],
            ['1970-01-31', false],
            [999, false],
            [true, false],
        ];
    }        

    //   Same problematic as in the date of birth

    #[DataProvider('provideDateOfEmployment')]
    public function testDateOfEmployment($value, $expected): void 
    {
        $res = $this->employee->setDateOfEmployment($value);

        $this->assertEquals($expected, $res);
    }
    public static function provideDateOfEmployment(): array
    {
        $today = new DateTimeImmutable();
        $format = 'd/m/Y';

        return [
            [$today->format($format), true],
            [$today->modify('-1 day')->format($format), true],
            [$today->modify('-10 days')->format($format), true],
            [$today->modify('-8 years')->format($format), true],
            [$today->modify('+1 day')->format($format), false],
            [$today->modify('+10 days')->format($format), false],
            [$today->modify('+8 years')->format($format), false],
            ['', false],
            ['31/02/1970', false],
            ['1970-01-31', false],
            [999, false],
            [true, false],
        ];
    }

    //   The actual salary is based on two input partitions: base salary and educational level.
    //   Both input partitions have already been tested.
    //   What is necessary to test here is whether the calculation takes place correctly.
    //   The cases to take into account are all positive:
    //   - Educational levels: 0, 1, 2, and 3

    #[DataProvider('provideSalary')]
    public function testSalary($value, $expected): void 
    {
        $this->employee->setBaseSalary($value[0]);
        $this->employee->setEducationalLevel($value[1]);
        $res = $this->employee->getSalary();

        $this->assertEquals($expected, $res);
    }
    public static function provideSalary(): array 
    {
        return [
            [[30000, 0], 30000],          
            [[30000, 1], 31220],          
            [[30000, 2], 32440],          
            [[30000, 3], 33660],          
            [[10000, 0], 0],          // AMR: Wrong input data. These two test cases helped
            [[110000, 0], 0],         // me introduce input validation in the method
        ];
    }

    //   Same problematic as in the date of birth, since the discount calculation is based 
    //   on the years of employment, which must be calculated relative to today (non-deterministic data)

    #[DataProvider('provideDiscount')]
    public function testDiscount($value, $expected): void 
    {
        $this->employee->setDateOfEmployment($value);
        $res = $this->employee->getDiscount();

        $this->assertEquals($expected, $res);
    }
    public static function provideDiscount(): array 
    {
        $today = new DateTimeImmutable();
        $format = 'd/m/Y';

        return [
            [$today->format($format), 0.0],
            [$today->modify('-1 year')->format($format), 0.5],
            [$today->modify('-10 years')->format($format), 5.0],
            [$today->modify('-15 years')->format($format), 7.5],
            [$today->modify('-23 years')->format($format), 11.5],
        ];
    }

    //   Because the shipping costs are calculated based on fixed text values,
    //   it is relevant to test with different spellings (discount should not apply)

    #[DataProvider('provideShippingCosts')]
    public function testShippingCosts($value, $expected): void 
    {
        $this->employee->setCountry($value);
        $res = $this->employee->getShippingCosts();

        $this->assertEquals($expected, $res);
    }
    public static function provideShippingCosts(): array 
    {
        return [
            ['Denmark', 0],          
            ['Norway', 0],          
            ['Sweden', 0],          
            ['Iceland', 50],          
            ['Finland', 50],          
            ['DENMARK', 100],          
            ['Spain', 100],          
            ['ABCDEFG', 100],          
            ['', 100],          
            [0, 100],          
            [true, 100],          
        ];       
    }
}