<?php

namespace App\DataFixtures;

use App\Entity\Department;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class DepartmentFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $departments = [
            ['name' => 'IT', 'description' => 'Information Technology'],
            ['name' => 'Marketing', 'description' => 'Marketing and Communications'],
            ['name' => 'HR', 'description' => 'Human Resources'],
            ['name' => 'Sales', 'description' => 'Sales Department'],
            ['name' => 'Finance', 'description' => 'Finance and Accounting'],
        ];

        foreach ($departments as $data) {
            $department = new Department();
            $department->setName($data['name']);
            $department->setDescription($data['description']);
            $manager->persist($department);
        }

        $manager->flush();
    }
}
