<?php

namespace OpenOrchestra\Backoffice\GenerateForm\Strategies;

use OpenOrchestra\DisplayBundle\DisplayBlock\Strategies\ContactStrategy as BaseContactStrategy;
use OpenOrchestra\ModelInterface\Model\BlockInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

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
        return BaseContactStrategy::NAME === $block->getComponent();
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('recipient', 'email', array(
            'label' => 'open_orchestra_backoffice.block.contact.recipient',
            'constraints' => new NotBlank(),
            'group_id' => 'data',
            'sub_group_id' => 'content',
        ));
        $builder->add('signature', 'text', array(
            'label' => 'open_orchestra_backoffice.block.contact.signature',
            'constraints' => new Email(),
            'group_id' => 'data',
            'sub_group_id' => 'content',
        ));
    }

    /**
     * @return array
     */
    public function getDefaultConfiguration()
    {
        return array(
            'maxAge' => 0
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'contact';
    }

}
