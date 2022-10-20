<?php

namespace WhatsappBundle\Common;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor as Executor,
    Doctrine\Common\DataFixtures\Purger\ORMPurger as Purger,
    Doctrine\Common\DataFixtures\Loader,
    Doctrine\Common\DataFixtures\ReferenceRepository,
    Symfony\Bundle\FrameworkBundle\Test\WebTestCase,
    Symfony\Bundle\FrameworkBundle\Console\Application;

abstract class AbstractTestCase extends WebTestCase
{
    
/**
     * Array of fixtures to load.
     */
    protected $fixtures = array();
    protected $client = null;

    /**
     * Setup test environment
     */
    public function setUp()
    {
        $this->client = static::createClient();
        //$kernel = static::createKernel(array('environment' => 'test', 'debug' => false));
        
        //$kernel->boot();
        //$this->container = $kernel->getContainer();
        $this->entityManager = $this->client->getContainer()->get('doctrine')->getManager(); 

        if ($this->fixtures) {
            $this->loadFixtures($this->fixtures, false);
        }
    }

    /**
     * Load fixtures
     *
     * @param array   $fixtures names of _fixtures to load
     * @param boolean $append   append data, or replace?
     */
    protected function loadFixtures($fixtures = array(), $append = true)
    {
        $defaultFixtures = false;

        $loader = new Loader();
        $refRepo = new ReferenceRepository($this->entityManager);

        foreach ((array) $fixtures as $name) {
            $fixture = new $name();
            //$fixture->setReferenceRepository($refRepo);
            $loader->addFixture($fixture);
        }

        $purger = new Purger();
        $executor = new Executor($this->entityManager, $purger);
        $executor->execute($loader->getFixtures(), $append);
    }
    
}
