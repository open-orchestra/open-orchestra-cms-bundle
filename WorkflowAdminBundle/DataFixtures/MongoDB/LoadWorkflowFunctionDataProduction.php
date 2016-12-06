<?php

namespace OpenOrchestra\WorkflowAdminBundle\DataFixtures\MongoDB;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use OpenOrchestra\ModelInterface\DataFixtures\OrchestraProductionFixturesInterface;
use OpenOrchestra\ModelBundle\Document\WorkflowFunction;

/**
 * Class LoadWorkflowFunctionDataProduction
 */
class LoadWorkflowFunctionDataProduction extends AbstractFixture implements OrderedFixtureInterface, OrchestraProductionFixturesInterface
{
    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        if (false == $this->hasReference('workflow-function-validator-functional')) {
            $workflowFunctionValidator = new WorkflowFunction();
            $workflowFunctionValidator->addName('en', 'Validator');
            $workflowFunctionValidator->addName('fr', 'Validateur');
            $workflowFunctionValidator->addRole($this->getReference('role-production-draft-to-published'));
            $this->addReference('workflow-function-validator-production', $workflowFunctionValidator);

            $manager->persist($workflowFunctionValidator);
            $manager->flush();
        }
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    public function getOrder()
    {
        return 125;
    }
}
