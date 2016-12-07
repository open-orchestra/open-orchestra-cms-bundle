<?php

namespace OpenOrchestra\WorkflowAdminBundle\DataFixtures\MongoDB;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use OpenOrchestra\ModelInterface\Model\WorkflowRightInterface;
use OpenOrchestra\ModelBundle\Document\Authorization;
use OpenOrchestra\ModelBundle\Document\WorkflowRight;
use OpenOrchestra\ModelInterface\DataFixtures\OrchestraFunctionalFixturesInterface;

/**
 * Class LoadWorkflowRightDataFunctional
 */
class LoadWorkflowRightDataFunctional extends AbstractFixture implements OrderedFixtureInterface,OrchestraFunctionalFixturesInterface
{
    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $workflowRight = new WorkflowRight();
        $workflowRight->setUserId($this->getReference('p-admin')->getId());

        $authorization = new Authorization();
        $authorization->setReferenceId(WorkflowRightInterface::NODE);
        $authorization->addWorkflowFunction($this->getReference('workflow-function-validator-functional'));
        $authorization->addWorkflowFunction($this->getReference('workflow-function-contributor-functional'));

        $workflowRight->addAuthorization($authorization);

        $manager->persist($workflowRight);
        $manager->flush();
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    public function getOrder()
    {
        return 1000;
    }
}
