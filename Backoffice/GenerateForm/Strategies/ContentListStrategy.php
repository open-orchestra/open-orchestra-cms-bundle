<?php

namespace PHPOrchestra\Backoffice\GenerateForm\Strategies;

use PHPOrchestra\DisplayBundle\DisplayBlock\DisplayBlockInterface;
use PHPOrchestra\ModelInterface\Model\BlockInterface;
use Symfony\Component\Form\FormInterface;
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
     * @param FormInterface  $form
     * @param BlockInterface $block
     */
    public function buildForm(FormInterface $form, BlockInterface $block)
    {
        $attributes = $block->getAttributes();

        $empty = array(
            'keywords' => null,
            'url' => '',
            'characterNumber' => 50,
            'choiceType' => ContentRepositoryInterface::CHOICE_AND,
            'contentType' => ''
        );

        $attributes = array_merge($empty, $attributes);

        $form->add('url', 'orchestra_node_choice', array(
            'mapped' => false,
            'data' => $attributes['url'],
            'label' => 'php_orchestra_backoffice.form.content_list.node',
        ));
        $form->add('characterNumber', 'text', array(
            'mapped' => false,
            'data' => $attributes['characterNumber'],
            'label' => 'php_orchestra_backoffice.form.content_list.nb_characters',
            'required' => false,
        ));
        $form->add('contentType', 'orchestra_content_type_choice', array(
            'mapped' => false,
            'data' => $attributes['contentType'],
            'label' => 'php_orchestra_backoffice.form.content_list.content_type',
            'required' => false
        ));
        $form->add('choiceType', 'choice', array(
            'mapped' => false,
            'data' => $attributes['choiceType'],
            'label' => 'php_orchestra_backoffice.form.content_list.choice_type',
            'required' => true,
            'choices' => array(
                ContentRepositoryInterface::CHOICE_AND => $this->translator->trans('php_orchestra_backoffice.form.content_list.choice_type_and'),
                ContentRepositoryInterface::CHOICE_OR => $this->translator->trans('php_orchestra_backoffice.form.content_list.choice_type_or'),
            ),
        ));
        $form->add('keywords', 'orchestra_keywords', array(
            'mapped' => false,
            'data' => $attributes['keywords'],
            'label' => 'php_orchestra_backoffice.form.content_list.content_keyword',
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
