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
        $nicolas->addRole('ROLE_FROM_PUBLISHED_TO_DRAFT');
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
        $benjamin->addRole('ROLE_FROM_DRAFT_TO_PENDING');
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
        $noel->addRole('ROLE_FROM_PENDING_TO_PUBLISHED');
        $noel->setEnabled(true);

        return $noel;
    }
}
