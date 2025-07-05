<?php

namespace App\Tests\Controller;

use App\Entity\Employee;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class EmployeeControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    private EntityRepository $employeeRepository;
    private string $path = '/employee/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->employeeRepository = $this->manager->getRepository(Employee::class);

        foreach ($this->employeeRepository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }

    public function testIndex(): void
    {
        $this->client->followRedirects();
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Employee index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'employee[name]' => 'Testing',
            'employee[email]' => 'Testing',
            'employee[phoneNumber]' => 'Testing',
            'employee[hireDate]' => 'Testing',
            'employee[department]' => 'Testing',
        ]);

        self::assertResponseRedirects($this->path);

        self::assertSame(1, $this->employeeRepository->count([]));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Employee();
        $fixture->setName('My Title');
        $fixture->setEmail('My Title');
        $fixture->setPhoneNumber('My Title');
        $fixture->setHireDate('My Title');
        $fixture->setDepartment('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Employee');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Employee();
        $fixture->setName('Value');
        $fixture->setEmail('Value');
        $fixture->setPhoneNumber('Value');
        $fixture->setHireDate('Value');
        $fixture->setDepartment('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'employee[name]' => 'Something New',
            'employee[email]' => 'Something New',
            'employee[phoneNumber]' => 'Something New',
            'employee[hireDate]' => 'Something New',
            'employee[department]' => 'Something New',
        ]);

        self::assertResponseRedirects('/employee/');

        $fixture = $this->employeeRepository->findAll();

        self::assertSame('Something New', $fixture[0]->getName());
        self::assertSame('Something New', $fixture[0]->getEmail());
        self::assertSame('Something New', $fixture[0]->getPhoneNumber());
        self::assertSame('Something New', $fixture[0]->getHireDate());
        self::assertSame('Something New', $fixture[0]->getDepartment());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();
        $fixture = new Employee();
        $fixture->setName('Value');
        $fixture->setEmail('Value');
        $fixture->setPhoneNumber('Value');
        $fixture->setHireDate('Value');
        $fixture->setDepartment('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/employee/');
        self::assertSame(0, $this->employeeRepository->count([]));
    }
}
