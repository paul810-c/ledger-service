<?php

namespace App\Tests;

use App\DataFixtures\LedgerBalanceFixture;
use App\DataFixtures\LedgerFixture;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

abstract class KernelTestCaseWithFixtures extends KernelTestCase
{
    protected ?EntityManagerInterface $em = null;

    protected function setUp(): void
    {
        self::bootKernel();

        /** @var EntityManagerInterface $em */
        $this->em = self::getContainer()->get('doctrine')->getManager();

        $loader = new Loader();
        $loader->addFixture(new LedgerFixture());
        // $loader->addFixture(new TransactionFixture());
        $loader->addFixture(new LedgerBalanceFixture());

        $purger = new ORMPurger($this->em);
        $executor = new ORMExecutor($this->em, $purger);

        $executor->execute($loader->getFixtures());
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->em?->clear();
        $this->em = null;
    }
}

