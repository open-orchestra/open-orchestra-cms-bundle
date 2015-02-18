<?php

namespace PHPOrchestra\Backoffice\GenerateForm\Strategies;

use PHPOrchestra\DisplayBundle\DisplayBlock\DisplayBlockInterface;
use PHPOrchestra\ModelInterface\Model\BlockInterface;
use PHPOrchestra\ModelInterface\Model\NodeInterface;
use Symfony\Component\Form\FormBuilderInterface;
use PHPOrchestra\ModelInterface\Repository\ContentRepositoryInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class ContentListStrategy
 */
class ContentListStrategy extends AbstractBlockStrategy
{
    protected $translator;

    /**
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @param BlockInterface $block
     *
     * @return bool
     */
    public function support(BlockInterface $block)
    {
        return DisplayBlockInterface::CONTENT_LIST === $block->getComponent();
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('url', 'orchestra_node_choice', array(
            'empty_data' => NodeInterface::ROOT_NODE_ID,
            'label' => 'php_orchestra_backoffice.form.content_list.node',
        ));

        $builder->add('characterNumber', 'text', array(
            'empty_data' => 50,
            'label' => 'php_orchestra_backoffice.form.content_list.nb_characters',
            'required' => false,
        ));
        $builder->add('contentType', 'orchestra_content_type_choice', array(
            'label' => 'php_orchestra_backoffice.form.content_list.content_type',
            'required' => false
        ));
        $builder->add('choiceType', 'choice', array(
            'empty_data' => ContentRepositoryInterface::CHOICE_AND,
            'label' => 'php_orchestra_backoffice.form.content_list.choice_type',
            'required' => true,
            'choices' => array(
                ContentRepositoryInterface::CHOICE_AND => $this->translator->trans('php_orchestra_backoffice.form.content_list.choice_type_and'),
                ContentRepositoryInterface::CHOICE_OR => $this->translator->trans('php_orchestra_backoffice.form.content_list.choice_type_or'),
            ),
        ));
        /*$builder->add('keywords', 'orchestra_keywords', array(
            'required' => false,
            'label' => 'php_orchestra_backoffice.form.content_list.content_keyword',
        ));*/
        $builder->add('contentTemplate', 'tinymce', array(
            'required' => false,
            'label' => 'php_orchestra_backoffice.form.content_list.content_template',
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'content_list';
    }
}
