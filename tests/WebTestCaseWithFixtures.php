<?php

namespace App\Tests;

use App\DataFixtures\LedgerBalanceFixture;
use App\DataFixtures\LedgerFixture;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class WebTestCaseWithFixtures extends WebTestCase
{
    protected ?ObjectManager $em = null;

    protected function setUp(): void
    {
        self::ensureKernelShutdown();
    }

    protected function loadFixtures(): void
    {
        $this->em = static::getContainer()->get('doctrine')->getManager();

        $purger = new ORMPurger($this->em);
        $purger->purge();

        $loader = new Loader();
        $loader->addFixture(new LedgerFixture());
        $loader->addFixture(new LedgerBalanceFixture());

        $executor = new ORMExecutor($this->em, $purger);
        $executor->execute($loader->getFixtures());
    }
}
