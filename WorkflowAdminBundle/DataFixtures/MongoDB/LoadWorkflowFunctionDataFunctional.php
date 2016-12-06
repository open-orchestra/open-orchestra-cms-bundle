<?php

namespace OpenOrchestra\WorkflowAdminBundle\DataFixtures\MongoDB;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use OpenOrchestra\ModelBundle\Document\WorkflowFunction;
use OpenOrchestra\ModelInterface\DataFixtures\OrchestraFunctionalFixturesInterface;

/**
 * Class LoadWorkflowFunctionDataFunctional
 */
class LoadWorkflowFunctionDataFunctional extends AbstractFixture implements OrderedFixtureInterface, OrchestraFunctionalFixturesInterface
{
    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $workflowFunctionValidator = new WorkflowFunction();
        $workflowFunctionValidator->addName('en', 'Validator');
        $workflowFunctionValidator->addName('fr', 'Validateur');
        $workflowFunctionValidator->addRole($this->getReference('role-functional-draft-to-pending'));
        $workflowFunctionValidator->addRole($this->getReference('role-functional-pending-to-published'));
        $this->addReference('workflow-function-validator-functional', $workflowFunctionValidator);

        $workflowFunctionContributor = new WorkflowFunction();
        $workflowFunctionContributor->addName('en', 'Contributor');
        $workflowFunctionContributor->addName('fr', 'Contributeur');
        $workflowFunctionContributor->addRole($this->getReference('role-functional-published-to-draft'));
        $this->addReference('workflow-function-contributor-functional', $workflowFunctionContributor);

        $manager->persist($workflowFunctionValidator);
        $manager->persist($workflowFunctionContributor);
        $manager->flush();
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    public function getOrder()
    {
        return 120;
    }
}

