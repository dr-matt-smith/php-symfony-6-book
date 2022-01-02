<?php

namespace App\Repository;

use App\Entity\Student;

class StudentRepository
{
    private array $students = [];

    public function __construct()
    {
        $id = 1;
        $s1 = new Student();
        $s1->setId(1);
        $s1->setFirstName('matt');
        $s1->setSurname('smith');

        $this->students[$id] = $s1;

        $id = 2;
        $s2 = new Student();
        $s2->setId(2);
        $s2->setFirstName('joelle');
        $s2->setSurname('murphy');

        $this->students[$id] = $s2;

        $id = 3;
        $s3 = new Student();
        $s3->setId(3); 
        $s3->setFirstName('frances');
        $s3->setSurname('mcguinness');

        $this->students[$id] = $s3;
    }

    public function findAll()
    {
        return $this->students;
    }

    public function find($id)
    {
        if (array_key_exists($id, $this->students)) {
            return $this->students[$id];
        } else {
            return null;
        }
    }
}