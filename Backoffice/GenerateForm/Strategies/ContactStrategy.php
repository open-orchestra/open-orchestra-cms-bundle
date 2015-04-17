<?php

namespace OpenOrchestra\Backoffice\GenerateForm\Strategies;

use OpenOrchestra\DisplayBundle\DisplayBlock\Strategies\ContactStrategy as BaseContactStrategy;
use OpenOrchestra\ModelInterface\Model\BlockInterface;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class ContactStrategy
 */
class ContactStrategy extends AbstractBlockStrategy
{
    /**
     * @param BlockInterface $block
     *
     * @return bool
     */
    public function support(BlockInterface $block)
    {
        return BaseContactStrategy::CONTACT === $block->getComponent();
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('recipient', 'email', array(
            'label' => 'open_orchestra_backoffice.block.contact.recipient'
        ));
        $builder->add('signature', 'text', array(
            'label' => 'open_orchestra_backoffice.block.contact.signature'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'contact';
    }

}
