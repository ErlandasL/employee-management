<?php

namespace App\Controller;

use App\Entity\Employee;
use App\Form\EmployeeForm;
use App\Repository\EmployeeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Component\Form\FormError;
use Knp\Component\Pager\PaginatorInterface;
use App\Repository\DepartmentRepository;

#[Route('/employee')]
final class EmployeeController extends AbstractController
{
    #[Route(name: 'app_employee_index', methods: ['GET'])]
    public function index(
        EmployeeRepository $employeeRepository,
        DepartmentRepository $departmentRepository,
        Request $request,
        PaginatorInterface $paginator
    ): Response {
        $searchQuery = $request->query->get('q');
        $departmentId = $request->query->get('department');
        $department = null;

        if ($departmentId) {
            $department = $departmentRepository->find($departmentId);
        }

        $queryBuilder = $employeeRepository->createQueryBuilderForSearch(
            $searchQuery,
            $department
        );

        $pagination = $paginator->paginate(
            $queryBuilder->getQuery(),
            $request->query->getInt('page', 1),
            5 
        );

        $departments = $departmentRepository->findAll();

        return $this->render('employee/index.html.twig', [
            'pagination' => $pagination,
            'departments' => $departments,
            'currentSearchQuery' => $searchQuery,
            'currentDepartmentId' => $departmentId,
        ]);
    }

    #[Route('/new', name: 'app_employee_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $employee = new Employee();
        $form = $this->createForm(EmployeeForm::class, $employee);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $entityManager->persist($employee);
                $entityManager->flush();

                $this->addFlash('success', 'Employee created successfully!');
                return $this->redirectToRoute('app_employee_index', [], Response::HTTP_SEE_OTHER);
            } catch (UniqueConstraintViolationException $e) {
                $form->get('email')->addError(new FormError('This email is already used. Please use a different email.'));
            } catch (\Exception $e) {
                $this->addFlash('error', 'An error occurred while saving the employee. Please try again.');
            }
        }

        return $this->render('employee/new.html.twig', [
            'employee' => $employee,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_employee_show', methods: ['GET'])]
    public function show(Employee $employee): Response
    {
        return $this->render('employee/show.html.twig', [
            'employee' => $employee,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_employee_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Employee $employee, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EmployeeForm::class, $employee);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $entityManager->flush();

                $this->addFlash('success', 'Employee updated successfully!'); // Optional: Add a success flash message
                return $this->redirectToRoute('app_employee_index', [], Response::HTTP_SEE_OTHER);
            } catch (UniqueConstraintViolationException $e) {
                $form->get('email')->addError(new FormError('This email is already used. Please use a different email.'));
            } catch (\Exception $e) {
                $this->addFlash('error', 'An error occurred while updating the employee. Please try again.');
            }
        }

        return $this->render('employee/edit.html.twig', [
            'employee' => $employee,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_employee_delete', methods: ['POST'])]
    public function delete(Request $request, Employee $employee, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$employee->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($employee);
            $entityManager->flush();
            $this->addFlash('success', 'Employee deleted successfully!'); // Optional: Add a success flash message
        }

        return $this->redirectToRoute('app_employee_index', [], Response::HTTP_SEE_OTHER);
    }
}
