<?php

namespace OpenOrchestra\GroupBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use OpenOrchestra\BackofficeBundle\Model\GroupInterface;
use OpenOrchestra\Backoffice\Manager\TranslationChoiceManager;

/**
 * Class GroupDocumentType
 */
class GroupDocumentType extends AbstractType
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
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'oo_group_document';
    }

    /**
     * @return string
     */
    public function getParent()
    {
        return 'document';
    }
}
