<?php

namespace OpenOrchestra\BackofficeBundle\Form\Type\Component;

use OpenOrchestra\Backoffice\Collector\RoleCollectorInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\AbstractType;

/**
 * Class RoleChoiceType
 */
class RoleChoiceType extends AbstractType
{
    protected $roleCollector;
    protected $name;

    /**
     * @param RoleCollectorInterface $roleCollector
     * @param string                 $name
     */
    public function __construct(RoleCollectorInterface $roleCollector, $name)
    {
        $this->roleCollector = $roleCollector;
        $this->name = $name;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'choices' => $this->roleCollector->getRoles()
        ));
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getParent()
    {
        return 'choice';
    }
}
