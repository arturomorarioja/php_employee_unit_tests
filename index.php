<?php

require_once 'classes/employee.php';

$jonas = new Employee;

echoBr ($jonas->setCpr('1234567890') ? 'Good: ' .  $jonas->getCpr() : 'Bad');
echoBr ($jonas->setFirstName('Jonas -L') ? 'Good: ' .  $jonas->getFirstName() : 'Bad');
echoBr ($jonas->setLastName('Madsen-Perkinsen') ? 'Good: ' .  $jonas->getLastName() : 'Bad');
echoBr ($jonas->setDepartment('IT') ? 'Good: ' .  $jonas->getDepartment() : 'Bad');
echoBr ($jonas->setBaseSalary(30000) ? 'Good: ' .  $jonas->getBaseSalary() : 'Bad');
echoBr ($jonas->setEducationalLevel(2) ? 'Good: ' .  $jonas->getEducationalLevel() : 'Bad');
echoBr ($jonas->setDateOfBirth('13/09/2004') ? 'Good: ' .  $jonas->getDateOfBirth() : 'Bad');
echoBr ($jonas->setDateOfEmployment('13/09/2011') ? 'Good: ' .  $jonas->getDateOfEmployment() : 'Bad');
echoBr ($jonas->setCountry('Denmark') ? 'Good: ' .  $jonas->getCountry() : 'Bad');

echoBr ();
echoBr ('Salary: ' . $jonas->getSalary());
echoBr ('Discount: ' . $jonas->getDiscount());
echoBr ('Shipping costs: ' . $jonas->getShippingCosts());

function echoBr($text = '') {
    echo $text . '<br>';
}