<?php

/**
 * Class Employee
 * @author  Arturo Mora-Rioja
 * @version 1.0, September 2022
 */

class Employee {

    public const DATE_FORMAT = 'd/m/Y';
    private const DEPARTMENTS = ['HR', 'Finance', 'IT', 'Sales', 'General Services'];
    private const EDU_LEVELS = ['None', 'Primary', 'Secondary', 'Tertiary'];
    private const COUNTRIES_NO_SHIPPING_COSTS = ['Denmark', 'Norway', 'Sweden'];
    private const COUNTRIES_HALF_SHIPPING_COSTS = ['Iceland', 'Finland'];

    private string $cpr;    
    private string $firstName;
    private string $lastName;
    private string $department;
    private float $baseSalary;
    private int $eduLevel = 0;
    private string $birthDate;
    private string $employmentDate;
    private string $country;    

    public function setCpr(string $cpr): bool {
        if (!preg_match('/^[0-9]{10}$/', $cpr)) {
            return false;
        } else {
            $this->cpr = $cpr;
            return true;
        };
    }

    private function nameIsValid(string $name): bool {
        return ((strlen($name) > 0) && (strlen($name) <= 30) && (preg_match('/^[a-zA-ZæøåñçáéíóúàèìòùäëïöüâêîôûÆØÅÑÇÁÉÍÓÚÀÈÌÒÙÄËÏÖÜÂÊÎÔÛ \-]+$/i', $name)));
    }

    public function setFirstName(string $firstName): bool {
        if (!$this->nameIsValid($firstName)) {
            return false;
        } else {            
            $this->firstName = $firstName;
            return true;
        }
    }

    public function setLastName(string $lastName): bool {
        if (!$this->nameIsValid($lastName)) {
            return false;
        } else {            
            $this->lastName = $lastName;
            return true;
        }
    }

    public function setDepartment(string $department): bool {
        if (!in_array($department, self::DEPARTMENTS)) {
            return false;
        } else {
            $this->department = $department;
            return true;
        }
    }

    public function setBaseSalary(float $salary): bool {
        if ($salary < 20000 || $salary > 100000) {
            return false;
        } else {
            $this->baseSalary = floor($salary * 100) / 100;
            return true;
        }
    }

    public function setEducationalLevel(int $eduLevel): bool {
        if ($eduLevel < 0 || $eduLevel > 3) {
            return false;
        } else {
            $this->eduLevel = $eduLevel;
            return true;
        }
    }

    private function formatDate(string $date): string {
        $date = trim($date);
        if (!DateTime::createFromFormat(self::DATE_FORMAT, $date)) {
            return false;
        } elseif (!checkdate(substr($date, 3, 2), substr($date, 0, 2), substr($date, 6, 4))) {
            return false;
        } else {
            return date(self::DATE_FORMAT, strtotime($this->formatDateAmerican($date)));
        }        
    }

    private function formatDateAmerican(string $date): string {
        return substr($date, 6, 4) . '/' . substr($date, 3, 2) . '/' . substr($date, 0, 2);
    }

    public function setDateOfBirth(string $birthDate): bool {
        if (!$dob = $this->formatDate($birthDate)) {
            return false;
        } else {
            $dob = $this->formatDateAmerican($dob);
            if (date_diff(date_create($dob), date_create())->format('%y') < 18) {
                return false;
            } else {
                $this->birthDate = $birthDate;
                return true;
            }
        }
    }

    public function setDateOfEmployment(string $employmentDate): bool {
        if (!$doe = $this->formatDate($employmentDate)) {
            return false;
        } else {
            if (date('Y/m/d') < $this->formatDateAmerican($doe)) {
                return false;
            } else {
                $this->employmentDate = $employmentDate;
                return true;
            }
        }
    }

    public function setCountry(string $country): bool {
        $this->country = $country;
        return true;
    }

    public function getCpr():               string {    return (isset($this->cpr) ? $this->cpr : ''); }
    public function getFirstName():         string {    return (isset($this->firstName) ? $this->firstName : ''); }
    public function getLastName():          string {    return (isset($this->lastName) ? $this->lastName : ''); }
    public function getDepartment():        string {    return (isset($this->department) ? $this->department : ''); }
    public function getBaseSalary():        float {     return (isset($this->baseSalary) ? $this->baseSalary : 0); }
    public function getEducationalLevel():  string {    return self::EDU_LEVELS[$this->eduLevel]; }
    public function getDateOfBirth():       string {    return (isset($this->birthDate) ? $this->birthDate : ''); }  
    public function getDateOfEmployment():  string {    return (isset($this->employmentDate) ? $this->employmentDate : ''); }  
    public function getCountry():           string {    return (isset($this->country) ? $this->country : ''); }

    public function getSalary(): float {
        if (!isset($this->baseSalary)) {
            return false;
        } else {
            return $this->baseSalary + ($this->eduLevel * 1220);
        }
    }

    public function getDiscount(): float {
        if (!isset($this->employmentDate)) {
            return false;
        } else {
            $doe = $this->formatDateAmerican($this->employmentDate);
            return date_diff(date_create($doe), date_create())->format('%y') * 0.5;
        }
    }

    public function getShippingCosts(): int {
        if (in_array($this->country, self::COUNTRIES_NO_SHIPPING_COSTS)) {
            return 0;
        } elseif (in_array($this->country, self::COUNTRIES_HALF_SHIPPING_COSTS)) {
            return 50;
        } else {
            return 100;
        }
    }
}