<?php

namespace OpenOrchestra\ModelLogBundle\DataFixtures\MongoDB;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use OpenOrchestra\ModelInterface\DataFixtures\OrchestraFunctionalFixturesInterface;
use OpenOrchestra\ModelLogBundle\Document\Log;

/**
 * Class LoadLogData
 */
class LoadLogData extends AbstractFixture implements OrderedFixtureInterface, OrchestraFunctionalFixturesInterface
{
    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $this->objectManager = $manager;

        $logProvider = array(
//             datetime, ip, user, site
            array('2016-02-10', '192.168.0.1', 'admin'    , 'fixture'),
            array('2016-02-10', '192.168.0.2', 'admin'    , '3'),
            array('2016-02-10', '192.168.0.3', 'admin'    , 'fixture'),
            array('2016-02-10', '192.168.0.4', 'admin'    , '3'),
            array('2016-02-10', '192.168.0.5', 'developer', 'fixture'),
            array('2016-02-10', '192.168.0.6', 'admin'    , '4'),
            array('2016-02-10', '192.168.0.7', 'admin'    , 'fixture'),
            array('2016-02-10', '192.168.0.8', 'admin'    , '4'),
            array('2016-02-10', '192.168.0.9', 'admin'    , 'fixture'),
            array('2017-01-01', '192.168.0.1', 'developer', '3'),
            array('2017-01-01', '192.168.0.2', 'developer', 'fixture'),
            array('2017-01-01', '192.168.0.3', 'developer', '4'),
            array('2017-01-01', '192.168.0.4', 'developer', 'fixture'),
            array('2017-01-01', '192.168.0.5', 'developer', '5'),
            array('2017-01-01', '192.168.0.5', 'developer', 'fixture'),
            array('2017-01-01', '192.168.0.5', 'admin'    , '3'),
            array('2017-01-01', '192.168.0.5', 'admin'    , 'fixture'),
        );
        foreach ($logProvider as $logParameters) {
            $log = $this->generateLog($logParameters[0], $logParameters[1], $logParameters[2], $logParameters[3]);
            $this->objectManager->persist($log);
        }

        $manager->flush();
    }

    protected function generateLog($dateTime, $ip, $name, $siteId)
    {
        $log = new Log();
        $log->setMessage('Imported log from fixtures');
        $log->setDateTime($dateTime);
        $log->setExtra(array('user_ip' => $ip, 'user_name' => $name, 'site_id' => $siteId));

        return $log;
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    public function getOrder()
    {
        return 10;
    }
}
