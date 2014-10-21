<?php

namespace PHPOrchestra\BackofficeBundle\Form\Type;

use PHPOrchestra\ModelBundle\Model\SiteableInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class OrchestraSiteType
 */
class OrchestraSiteType extends AbstractType
{
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'class' => 'PHPOrchestra\ModelBundle\Document\Site',
                'property' => 'domain',
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

    /**
     * @return string
     */
    public function getName()
    {
        return 'orchestra_site';
    }
}
