<?php

namespace OpenOrchestra\GroupBundle\Form\Type;

use OpenOrchestra\BackofficeBundle\Form\Type\AbstractGroupChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use OpenOrchestra\BackofficeBundle\Model\GroupInterface;
use OpenOrchestra\Backoffice\Manager\TranslationChoiceManager;

/**
 * Class GroupDocumentType
 */
class GroupDocumentType extends AbstractGroupChoiceType
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
