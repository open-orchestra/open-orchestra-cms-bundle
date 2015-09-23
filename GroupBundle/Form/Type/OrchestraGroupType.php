<?php

namespace OpenOrchestra\GroupBundle\Form\Type;

use OpenOrchestra\BackofficeBundle\Form\Type\AbstractOrchestraGroupType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use OpenOrchestra\BackofficeBundle\Model\GroupInterface;
use OpenOrchestra\Backoffice\Manager\TranslationChoiceManager;

/**
 * Class OrchestraGroupType
 */
class OrchestraGroupType extends AbstractOrchestraGroupType
{
    protected $groupClass;
    protected $translationChoiceManager;

    /**
     * @param string                   $groupClass
     * @param TranslationChoiceManager $translationChoiceManager
     */
    public function __construct($groupClass, TranslationChoiceManager $translationChoiceManager)
    {
        $this->groupClass = $groupClass;
        $this->translationChoiceManager = $translationChoiceManager;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $translationChoiceManager = $this->translationChoiceManager;
        $resolver->setDefaults(
            array(
                'class' => $this->groupClass,
                'choice_label' => function (GroupInterface $choice) use ($translationChoiceManager) {
                    return $translationChoiceManager->choose($choice->getLabels());
                },
            )
        );
    }

    /**
     * @return string
     */
    public function getParent()
    {
        return 'document';
    }
}
