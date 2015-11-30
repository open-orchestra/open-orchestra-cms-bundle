<?php

namespace OpenOrchestra\Backoffice\GenerateForm\Strategies;

use OpenOrchestra\BackofficeBundle\Validator\Constraints\ContentTemplate;
use OpenOrchestra\DisplayBundle\DisplayBlock\Strategies\ContentListStrategy as BaseContentListStrategy;
use OpenOrchestra\ModelInterface\Model\BlockInterface;
use OpenOrchestra\ModelInterface\Model\NodeInterface;
use Symfony\Component\Form\FormBuilderInterface;
use OpenOrchestra\ModelInterface\Repository\ContentRepositoryInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;

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
        return BaseContentListStrategy::CONTENT_LIST === $block->getComponent();
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('contentNodeId', 'oo_node_choice', array(
            'label' => 'open_orchestra_backoffice.form.content_list.node',
            'constraints' => new NotBlank(),
        ));
        $builder->add('characterNumber', 'integer', array(
            'empty_data' => 50,
            'constraints' => new Type('integer'),
            'label' => 'open_orchestra_backoffice.form.content_list.nb_characters',
            'required' => false,
        ));
        $builder->add('contentType', 'oo_content_type_choice', array(
            'label' => 'open_orchestra_backoffice.form.content_list.content_type',
            'required' => false
        ));
        $builder->add('choiceType', 'oo_operator_choice', array(
            'label' => 'open_orchestra_backoffice.form.content_list.choice_type',
        ));
        $builder->add('keywords', 'oo_keywords_choice', array(
            'embedded' => false,
            'required' => false,
            'label' => 'open_orchestra_backoffice.form.content_list.content_keyword',
        ));
        $builder->add('contentTemplateEnabled', 'checkbox', array(
            'label' => 'open_orchestra_backoffice.form.content_list.content_template_enabled.title',
            'required' => false,
            'attr' => array('help_text' => 'open_orchestra_backoffice.form.content_list.content_template_enabled.helper'),
        ));
        $builder->add('contentTemplate', 'oo_tinymce', array(
            'required' => false,
            'label' => 'open_orchestra_backoffice.form.content_list.content_template',
            'constraints' => new ContentTemplate(),
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
