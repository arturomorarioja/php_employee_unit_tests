<?php

/**
 * Unit tests for the Employee class
 * @author  Arturo Mora-Rioja
 * @version  1.0, September 2022
 */

require_once 'classes/employee.php';

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class EmployeeTest extends TestCase {
    private Employee $employee;
    private const STRING_30_CHARS = [
        ['A', true],                                // Valid lower boundary
        ['AB', true],                               // Valid lower boundary + 1 (3-value approach)
        ['ABCDEFGHIJKLMNOPQRSTUVWXYZABCD', true],   // Valid upper boundary
        ['ABCDEFGHIJKLMNOPQRSTUVWXYZABC', true],    // Valid upper boundary - 1 (3-value approach)
        ['ABCDEFGHIJKLMN', true],                   // Middle partition value
        ['abcdefghijklmn', true],                   // Middle partition value
        ['æøåñç', true],                            // AMR: I found this out thanks to this unit test!
        ['áéíóúàèìòùäëïöü', true],                  // AMR: I found this out thanks to this unit test!
        ['âêîôû', true],                            // AMR: I found this out thanks to this unit test!
        ['ÆØÅÑÇ', true],                            // AMR: I found this out thanks to this unit test!
        ['ÁÉÍÓÚÀÈÌÒÙÄËÏÖÜ', true],                  // AMR: I found this out thanks to this unit test!
        ['ÂÊÎÔÛ', true],                            // AMR: I found this out thanks to this unit test!
        ['a a a a a a a', true],                   
        ['a-a-a-a-a-a-a', true],                   
        ['-', true],                                
        [' ', true],                                
        ['', false],                                // Invalid lower boundary
        ['ABCDEFGHIJKLMNOPQRSTUVWXYZABCDE', false], // Invalid upper boundary
        ['abcdef1', false],                   
        ['abcdef/', false],                   
        ['abcdef,', false],                   
    ];

    public function setUp(): void 
    {
        $this->employee = new Employee;
    }

    public function tearDown(): void 
    {
        unset($this->employee);
    }

    #[DataProvider('provideCPR')]
    public function testCpr($value, $expected): void 
    {
        $res = $this->employee->setCpr($value);

        $this->assertEquals($expected, $res);
    }
    public static function provideCpr(): array 
    {
        return [
            ['1234567890', true],   // Valid upper and lower boundary
            ['0000000000', true],
            ['9999999999', true],
            ['0999999999', true],
            [1234567890, true],     // PHP automatically converts a 10-digit int to a string
            ['99999999999', false], // Invalid upper boundary
            ['999999999', false],   // Invalid lower boundary
            [12345678901, false],   // Invalid upper boundary
            [123456789, false],     // Invalid lower boundary
            ['ABCDEFGHIJ', false],
            [true, false],
            ['          ', false],
            ['', false],
            // [null, false],       // TypeError
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

    #[DataProvider('provideDepartment')]
    public function testDepartment($value, $expected): void 
    {
        $res = $this->employee->setDepartment($value);

        $this->assertEquals($expected, $res);
    }
    public static function provideDepartment(): array 
    {
        return [
            ['HR', true],
            ['Finance', true],
            ['IT', true],
            ['Sales', true],
            ['General Services', true],
            ['Bonds', false],
            ['', false],
            [0, false],
        ];
    }

    #[DataProvider('provideBaseSalary')]
    public function testBaseSalary($value, $expected): void 
    {
        $res = $this->employee->setBaseSalary($value);

        $this->assertEquals($expected, $res);
    }
    public static function provideBaseSalary(): array 
    {
        return [
            [20000, true],          // Valid lower boundary
            [20000.01, true],       // Valid lower boundary + 1 (3-value approach)
            [60000, true],          // Middle value for the valid input partition
            [100000, true],         // Valid upper boundary
            [99999.99, true],       // Valid upper boundary - 1 (3-value approach)
            [19999.99, false],      // Invalid lower boundary
            [100000.01, false],     // Invalid upper boundary
            [10000, false],         // Middle value for the invalid lower partition
            [110000, false],        // Middle value for the invalid upper partition
            [0, false],
            [-1, false],
            [-10000, false],
            [false, false],
        ];
    }

    #[DataProvider('provideEducationalLevel')]
    public function testEducationalLevel($value, $expected): void 
    {
        $res = $this->employee->setEducationalLevel($value);

        $this->assertEquals($expected, $res);
    }
    public static function provideEducationalLevel(): array
    {
        return [
            [0, true],          
            [1, true],          
            [2, true],          
            [3, true],          
            [-1, false],
            [4, false],
            [10, false],
            [-10, false],
        ];
    }

    #[DataProvider('provideDateOfBirth')]
    public function testDateOfBirth($value, $expected): void 
    {
        $res = $this->employee->setDateOfBirth($value);

        $this->assertEquals($expected, $res);
    }
    public static function provideDateOfBirth(): array
    {
        /* 
            PROBLEM
            Business calculations should never be part of a unit test,
            but there is no walkaround, since this test depends on the current date.
            "Eighteen years ago" cannot be hardcoded.
        */
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
            [0, 100],          
            [true, 100],          
        ];       
    }
}