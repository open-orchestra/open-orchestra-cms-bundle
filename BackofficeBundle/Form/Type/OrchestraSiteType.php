<?php

namespace PHPOrchestra\BackofficeBundle\Form\Type;

use Doctrine\ODM\MongoDB\DocumentRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

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
                'query_builder' => function (DocumentRepository $dr) {
                    return $dr->createQueryBuilder('s')->field('deleted')->equals(false);
                }
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
