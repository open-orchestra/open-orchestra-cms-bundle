<?php

namespace OpenOrchestra\GroupBundle\Form\Type;

use OpenOrchestra\Backoffice\Form\Type\AbstractGroupChoiceType;
use OpenOrchestra\Backoffice\Manager\MultiLanguagesChoiceManagerInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use OpenOrchestra\Backoffice\Model\GroupInterface;

/**
 * Class GroupDocumentType
 */
class GroupDocumentType extends AbstractGroupChoiceType
{
    protected $groupClass;
    protected $multiLanguagesChoiceManager;

    /**
     * @param string                               $groupClass
     * @param MultiLanguagesChoiceManagerInterface $multiLanguagesChoiceManager
     */
    public function __construct($groupClass, MultiLanguagesChoiceManagerInterface $multiLanguagesChoiceManager)
    {
        $this->groupClass = $groupClass;
        $this->multiLanguagesChoiceManager = $multiLanguagesChoiceManager;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $multiLanguagesChoiceManager = $this->multiLanguagesChoiceManager;
        $resolver->setDefaults(
            array(
                'class' => $this->groupClass,
                'choice_label' => function (GroupInterface $choice) use ($multiLanguagesChoiceManager) {
                    return $multiLanguagesChoiceManager->choose($choice->getLabels());
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
