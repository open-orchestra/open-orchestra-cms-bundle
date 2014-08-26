<?php

namespace PHPOrchestra\BackofficeBundle\Form\Type;

use PHPOrchestra\ModelBundle\Model\StatusableInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class StatusType
 */
class StatusType extends AbstractType
{
    protected $choices;

    /**
     * Constructor
     *
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->choices[StatusableInterface::STATUS_PUBLISHED] = 'php_orchestra_backoffice.form.status.published';
        $this->choices[StatusableInterface::STATUS_DRAFT] = 'php_orchestra_backoffice.form.status.draft';
        $this->choices[StatusableInterface::STATUS_PENDING] = 'php_orchestra_backoffice.form.status.pending';
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'choices' => $this->choices,
            )
        );
    }

    /**
     * @return string
     */
    public function getParent()
    {
        return 'choice';
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'orchestra_status';
    }
}
