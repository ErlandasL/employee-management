<?php

namespace App\Repository;

use App\Entity\Employee;
use App\Entity\Department; // Import Department
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder; // Import QueryBuilder

/**
 * @extends ServiceEntityRepository<Employee>
 */
class EmployeeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Employee::class);
    }

    /**
     * Creates a QueryBuilder for searching and filtering employees.
     */
    public function createQueryBuilderForSearch(?string $searchQuery, ?Department $department): QueryBuilder
    {
        $qb = $this->createQueryBuilder('e')
                   ->leftJoin('e.department', 'd') // Join with department
                   ->addSelect('d'); // Select department to avoid N+1 queries if you display department name

        if ($searchQuery) {
            $qb->andWhere('e.name LIKE :query OR e.email LIKE :query')
               ->setParameter('query', '%' . $searchQuery . '%');
        }

        if ($department) {
            $qb->andWhere('e.department = :department')
               ->setParameter('department', $department);
        }

        $qb->orderBy('e.name', 'ASC'); // Order by name for consistency

        return $qb;
    }

    // You can add other methods here if needed, but the search method is key for this feature
}