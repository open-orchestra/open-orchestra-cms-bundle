<?php

namespace OpenOrchestra\WorkflowAdminBundle\DataFixtures\MongoDB;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use OpenOrchestra\ModelInterface\DataFixtures\OrchestraFunctionalFixturesInterface;
use OpenOrchestra\ModelBundle\Document\WorkflowTransition;
use OpenOrchestra\ModelBundle\Document\WorkflowProfile;

/**
 * Class LoadWorkflowProfileDataFunctional
 */
class LoadWorkflowProfileDataFunctional extends AbstractFixture implements OrderedFixtureInterface,OrchestraFunctionalFixturesInterface
{
    /**
     * Load workflow profiles data fixtures
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $transitionDraftToPending = $this->createTransition('status-draft', 'status-pending');
        $transitionToTranslateToPending = $this->createTransition('status-toTranslate', 'status-pending');
        $transitionPendingToPublished = $this->createTransition('status-pending', 'status-published');
        $transitionPublishedToDraft = $this->createTransition('status-published', 'status-draft');
        $transitionDraftToPublished = $this->createTransition('status-draft', 'status-published');

        $profileContributor = $this->createProfile(
            array('en' => 'Contributor', 'fr' => 'Contributeur'),
            array('en' => 'Member which must submit his content to validation for publication', 'fr' => 'Membre qui doit soumettre son contenu à validation pour publication'),
            array($transitionDraftToPending, $transitionToTranslateToPending),
            'Contributor'
        );
        $profileValidator = $this->createProfile(
            array('en' => 'Validator', 'fr' => 'Validateur'),
            array('en' => 'Member which can publish', 'fr' => 'Membre qui peut publier un contenu'),
            array($transitionPendingToPublished, $transitionPublishedToDraft, $transitionDraftToPublished),
            'Validator'
        );

        $manager->persist($profileContributor);
        $manager->persist($profileValidator);

        $manager->flush();
    }

    /**
     * @param string $statusFromReference
     * @param string $statusToReference
     *
     * @return WorkflowTransition $transition
     */
    protected function createTransition($statusFromReference, $statusToReference)
    {
        $transition = new WorkflowTransition();
        $transition->setStatusFrom($this->getReference($statusFromReference));
        $transition->setStatusTo($this->getReference($statusToReference));

        return $transition;
    }

    /**
     * @param array                     $labels
     * @param array                     $descriptions
     * @param array<WorkflowTransition> $transitions
     * @param string                    $referenceName
     *
     * @return WorkflowProfile
     */
    protected function createProfile(array $labels, array $descriptions, array $transitions, $referenceName)
    {
        $profile = new WorkflowProfile();
        $profile->setLabels($labels);
        $profile->setDescriptions($descriptions);


        foreach ($transitions as $transition) {
            $profile->addTransition($transition);
        }

        $this->addReference('profile-' . $referenceName, $profile);

        return $profile;
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    public function getOrder()
    {
        return 150;
    }
}
