<?php

namespace App\tests;

use App\Entity\Token;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

/**
 * Class TestFixtures
 * @package App\DataFixtures
 */
class TestCase extends WebTestCase
{
    /** @var Application $application */
    protected static $application;
    /** @var KernelBrowser $client */
    protected $client;
    /** @var  EntityManagerInterface $em */
    protected $em;

    /**
     * @throws \Exception
     */
    public function setUp()
    {
        $this->client = static::createClient();
        $this->em = self::$container->get('doctrine.orm.entity_manager');
        parent::setUp();
        $this->em = self::$container->get('doctrine.orm.entity_manager');
        if (!$this->em->getConnection()->getSchemaManager()->tablesExist(['users'])) {
            $migration = $this->runCommand('doctrine:migrations:migrate --no-interaction');
            if ($migration !== 0) $this->fail('Executing the doctrine migration file failed');
            self::runCommand('doctrine:fixtures:load --no-interaction --append');
        }
    }

    /**
     * @param User $user
     * @return object|null
     */
    protected function findToken(User $user)
    {
        $token = $this->em->getRepository(Token::class)->findOneBy([
            'user' => $user, 'type' => \App\Service\Token::AUTHENTICATION
        ]);
        $this->assertNotNull($token);
        return $token;
    }

    /**
     * @param string $command
     * @return int
     * @throws \Exception
     */
    protected static function runCommand(string $command)
    {
        $command = sprintf('%s --quiet', $command);
        return self::getApplication()->run(new StringInput($command));
    }

    /**
     * @return Application
     */
    protected static function getApplication()
    {
        if (null === self::$application) {
            $client = static::createClient();
            self::$application = new Application($client->getKernel());
            self::$application->setAutoExit(false);
        }
        return self::$application;
    }

    /**
     *
     */
    protected function tearDown()
    {
        parent::tearDown();
        $this->em->close();
        $this->em = null; // avoid memory leaks
    }
}