<?php

namespace OpenOrchestra\UserAdminBundle\DataFixtures\MongoDB;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use OpenOrchestra\UserBundle\Document\User;

/**
 * Class AbstractLoadUserData
 */
abstract class AbstractLoadUserData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * @param string      $name
     * @param string|null $group
     * @return User
     */
    protected function generate($name, $group = null)
    {
        $user = new User();

        $user->setFirstName($name);
        $user->setLastName($name);
        $user->setEmail($name.'@fixtures.com');
        $user->setUsername($name);
        $user->setPlainPassword($name);

        if ($group) {
            $user->addGroup($this->getReference($group));
        }

        $user->setEnabled(true);

        return $user;
    }
}
