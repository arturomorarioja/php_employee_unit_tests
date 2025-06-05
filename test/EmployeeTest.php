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
        // American date format needed for the calculations
        $sEighteenYearsAgo = date('Y-m-d', strtotime('18 years ago'));
        $dEighteenYearsAgo = date_create($sEighteenYearsAgo);
        
        date_add($dEighteenYearsAgo, date_interval_create_from_date_string('-1 days'));
        $sEighteenYearsAgoMinusOneDay = date_format($dEighteenYearsAgo, Employee::DATE_FORMAT);
        
        $dEighteenYearsAgo = date_create($sEighteenYearsAgo);
        date_add($dEighteenYearsAgo, date_interval_create_from_date_string('1 days'));
        $sEighteenYearsAgoPlusOneDay = date_format($dEighteenYearsAgo, Employee::DATE_FORMAT);
        
        $dEighteenYearsAgo = date_create($sEighteenYearsAgo);
        date_add($dEighteenYearsAgo, date_interval_create_from_date_string('-10 days'));
        $sEighteenYearsAgoMinusTenDays = date_format($dEighteenYearsAgo, Employee::DATE_FORMAT);
        
        $dEighteenYearsAgo = date_create($sEighteenYearsAgo);
        date_add($dEighteenYearsAgo, date_interval_create_from_date_string('10 days'));
        $sEighteenYearsAgoPlusTenDays = date_format($dEighteenYearsAgo, Employee::DATE_FORMAT);

        $dEighteenYearsAgo = date_create($sEighteenYearsAgo);
        date_add($dEighteenYearsAgo, date_interval_create_from_date_string('-8 years'));
        $sEighteenYearsAgoMinusEightYears = date_format($dEighteenYearsAgo, Employee::DATE_FORMAT);

        $dEighteenYearsAgo = date_create($sEighteenYearsAgo);
        date_add($dEighteenYearsAgo, date_interval_create_from_date_string('8 years'));
        $sEighteenYearsAgoPlusEightYears = date_format($dEighteenYearsAgo, Employee::DATE_FORMAT);

        // Danish date format needed for the Employee class
        $sEighteenYearsAgo = date(Employee::DATE_FORMAT, strtotime('18 years ago'));
        
        // AMR: The method was not working at all due to date format issues.
        //      I discovered it thanks to this unit test
        return [
            [$sEighteenYearsAgo, true],
            [$sEighteenYearsAgoMinusOneDay, true],
            [$sEighteenYearsAgoMinusTenDays, true],
            [$sEighteenYearsAgoMinusEightYears, true],
            [$sEighteenYearsAgoPlusOneDay, false],
            [$sEighteenYearsAgoPlusTenDays, false],
            [$sEighteenYearsAgoPlusEightYears, false],
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
        // American date format needed for the calculations
        $sToday = date('Y-m-d');
        $dToday = date_create($sToday);
        
        date_add($dToday, date_interval_create_from_date_string('-1 days'));
        $sYesterday = date_format($dToday, Employee::DATE_FORMAT);
        
        $dToday = date_create($sToday);
        date_add($dToday, date_interval_create_from_date_string('1 days'));
        $sTomorrow = date_format($dToday, Employee::DATE_FORMAT);
        
        $dToday = date_create($sToday);
        date_add($dToday, date_interval_create_from_date_string('-10 days'));
        $sTodayMinusTenDays = date_format($dToday, Employee::DATE_FORMAT);
        
        $dToday = date_create($sToday);
        date_add($dToday, date_interval_create_from_date_string('10 days'));
        $sTodayPlusTenDays = date_format($dToday, Employee::DATE_FORMAT);

        $dToday = date_create($sToday);
        date_add($dToday, date_interval_create_from_date_string('-8 years'));
        $sTodayMinusEightYears = date_format($dToday, Employee::DATE_FORMAT);

        $dToday = date_create($sToday);
        date_add($dToday, date_interval_create_from_date_string('8 years'));
        $sTodayPlusEightYears = date_format($dToday, Employee::DATE_FORMAT);

        // Danish date format needed for the Employee class
        $sToday = date(Employee::DATE_FORMAT, strtotime('18 years ago'));
        
        return [
            [$sToday, true],
            [$sYesterday, true],
            [$sTodayMinusTenDays, true],
            [$sTodayMinusEightYears, true],
            [$sTomorrow, false],
            [$sTodayPlusTenDays, false],
            [$sTodayPlusEightYears, false],
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
        // American date format needed for the calculations
        $sToday = date('Y-m-d');
        $dToday = date_create($sToday);
        
        date_add($dToday, date_interval_create_from_date_string('-1 years'));
        $sTodayMinusOneYear = date_format($dToday, Employee::DATE_FORMAT);
        
        $dToday = date_create($sToday);
        date_add($dToday, date_interval_create_from_date_string('-10 years'));
        $sTodayMinusTenYears = date_format($dToday, Employee::DATE_FORMAT);
        
        $dToday = date_create($sToday);
        date_add($dToday, date_interval_create_from_date_string('-15 years'));
        $sTodayMinusFifteenYears = date_format($dToday, Employee::DATE_FORMAT);

        $dToday = date_create($sToday);
        date_add($dToday, date_interval_create_from_date_string('-23 years'));
        $sTodayMinusTwentyThreeYears = date_format($dToday, Employee::DATE_FORMAT);

        // Danish date format needed for the Employee class
        $sToday = date('Y-m-d');
        
        return [
            [$sToday, 0],
            [$sTodayMinusOneYear, 0.5],
            [$sTodayMinusTenYears, 5],
            [$sTodayMinusFifteenYears, 7.5],
            [$sTodayMinusTwentyThreeYears, 11.5],
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