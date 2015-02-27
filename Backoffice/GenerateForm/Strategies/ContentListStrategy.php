<?php

namespace OpenOrchestra\Backoffice\GenerateForm\Strategies;

use OpenOrchestra\DisplayBundle\DisplayBlock\DisplayBlockInterface;
use OpenOrchestra\ModelInterface\Model\BlockInterface;
use OpenOrchestra\ModelInterface\Model\NodeInterface;
use Symfony\Component\Form\FormBuilderInterface;
use OpenOrchestra\ModelInterface\Repository\ContentRepositoryInterface;
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
        $builder->add('contentNodeId', 'orchestra_node_choice', array(
            'label' => 'open_orchestra_backoffice.form.content_list.node',
        ));
        $builder->add('characterNumber', 'text', array(
            'empty_data' => 50,
            'label' => 'open_orchestra_backoffice.form.content_list.nb_characters',
            'required' => false,
        ));
        $builder->add('contentType', 'orchestra_content_type_choice', array(
            'label' => 'open_orchestra_backoffice.form.content_list.content_type',
            'required' => false
        ));
        $builder->add('choiceType', 'choice', array(
            'empty_data' => ContentRepositoryInterface::CHOICE_AND,
            'label' => 'open_orchestra_backoffice.form.content_list.choice_type',
            'required' => true,
            'choices' => array(
                ContentRepositoryInterface::CHOICE_AND => $this->translator->trans('open_orchestra_backoffice.form.content_list.choice_type_and'),
                ContentRepositoryInterface::CHOICE_OR => $this->translator->trans('open_orchestra_backoffice.form.content_list.choice_type_or'),
            ),
        ));
        $builder->add('keywords', 'orchestra_keywords', array(
            'embedded' => false,
            'required' => false,
            'label' => 'open_orchestra_backoffice.form.content_list.content_keyword',
        ));
        $builder->add('contentTemplateEnabled', 'on_off', array(
            'label' => 'open_orchestra_backoffice.form.content_list.content_template_enabled',
        ));
        $builder->add('contentTemplate', 'tinymce', array(
            'required' => false,
            'label' => 'open_orchestra_backoffice.form.content_list.content_template',
        ));
    }

    /**
     * @return array
     */
    public function getDefaultConfiguration()
    {
        return array(
            'contentNodeId' => NodeInterface::ROOT_NODE_ID,
            'characterNumber' => '50',
            'contentTemplate' => '',
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'content_list';
    }
}
