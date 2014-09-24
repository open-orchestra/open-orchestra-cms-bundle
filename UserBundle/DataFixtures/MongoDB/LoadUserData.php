<?php

namespace PHPOrchestra\UserBundle\DataFixtures\MongoDB;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use PHPOrchestra\UserBundle\Document\User;

/**
 * Class LoadUserData
 */
class LoadUserData implements FixtureInterface
{
    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $nicolas = $this->generateNicolas();
        $manager->persist($nicolas);

        $benjamin = $this->generateBenjamin();
        $manager->persist($benjamin);

        $noel = $this->generateNoel();
        $manager->persist($noel);

        $manager->flush();
    }

    /**
     * @return User
     */
    protected function generateNicolas()
    {
        $nicolas = new User();

        $nicolas->setUsername('nicolas');
        $nicolas->setPlainPassword('nicolas');
        $nicolas->addRole('ROLE_ADMIN');
        $nicolas->addRole('ROLE_USER');
        $nicolas->addRole('ROLE_DRAFT');
        $nicolas->addRole('ROLE_PUBLISHED');
        $nicolas->setEnabled(true);

        return $nicolas;
    }

    /**
     * @return User
     */
    protected function generateBenjamin()
    {
        $benjamin = new User();

        $benjamin->setUsername('benjamin');
        $benjamin->setPlainPassword('benjamin');
        $benjamin->addRole('ROLE_ADMIN');
        $benjamin->addRole('ROLE_USER');
        $benjamin->addRole('ROLE_DRAFT');
        $benjamin->addRole('ROLE_PUBLISHED');
        $benjamin->setEnabled(true);

        return $benjamin;
    }

    /**
     * @return User
     */
    protected function generateNoel()
    {
        $noel = new User();

        $noel->setUsername('noel');
        $noel->setPlainPassword('noel');
        $noel->addRole('ROLE_ADMIN');
        $noel->addRole('ROLE_USER');
        $noel->addRole('ROLE_DRAFT');
        $noel->addRole('ROLE_PUBLISHED');
        $noel->setEnabled(true);

        return $noel;
    }
}
