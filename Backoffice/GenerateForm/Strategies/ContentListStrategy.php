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
        $builder->add('contentNodeId', 'orchestra_node_choice', array(
            'label' => 'open_orchestra_backoffice.form.content_list.node',
            'constraints' => new NotBlank(),
        ));
        $builder->add('characterNumber', 'text', array(
            'empty_data' => 50,
            'constraints' => new Type('integer'),
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
            'constraints' => new NotBlank(),
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
        $builder->add('contentTemplateEnabled', 'checkbox', array(
            'label' => 'open_orchestra_backoffice.form.content_list.content_template_enabled.title',
            'required' => false,
            'attr' => array('help_text' => 'open_orchestra_backoffice.form.content_list.content_template_enabled.helper'),
        ));
        $builder->add('contentTemplate', 'tinymce', array(
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
