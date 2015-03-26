<?php
namespace OpenOrchestra\GroupBundle\Form\Type;

use OpenOrchestra\BackofficeBundle\Form\Type\AbstractOrchestraGroupType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class OrchestraGroupType
 */
class OrchestraGroupType extends AbstractOrchestraGroupType
{
    /**
     * @var string
     */
    private $groupClass;

    /**
     * @param $groupClass
     */
    public function __construct($groupClass)
    {
        $this->groupClass = $groupClass;
    }

    /**
     * {@inheritDoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'class' => $this->groupClass,
                'property' => 'name'
            )
        );
    }

    /**
     * {@inheritDoc}
     */
    public function getParent()
    {
        return 'document';
    }
}
