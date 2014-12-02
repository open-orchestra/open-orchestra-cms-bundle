<?php

namespace PHPOrchestra\BackofficeBundle\Form\Type;

use Doctrine\ODM\MongoDB\DocumentRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class OrchestraThemeType
 */
class OrchestraThemeType extends AbstractType
{
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'class' => 'PHPOrchestra\ModelBundle\Document\Theme',
                'property' => 'name',
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
        return 'orchestra_theme';
    }
}
