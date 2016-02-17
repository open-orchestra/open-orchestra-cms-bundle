<?php

namespace OpenOrchestra\BackofficeBundle\Form\Type;

use OpenOrchestra\Backoffice\EventSubscriber\NodeThemeSelectionSubscriber;
use OpenOrchestra\Backoffice\Manager\NodeManager;
use OpenOrchestra\Backoffice\EventSubscriber\AreaCollectionSubscriber;
use OpenOrchestra\Backoffice\EventSubscriber\NodeTemplateSelectionSubscriber;
use OpenOrchestra\ModelInterface\Repository\SiteRepositoryInterface;
use OpenOrchestra\ModelInterface\Repository\TemplateRepositoryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use OpenOrchestra\ModelInterface\Model\SchemeableInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class NodeType
 */
class NodeType extends AbstractType
{
    protected $areaClass;
    protected $translator;
    protected $nodeClass;
    protected $nodeManager;
    protected $templateRepository;
    protected $siteRepository;
    protected $schemeChoices;

    /**
     * @param string                      $nodeClass
     * @param TemplateRepositoryInterface $templateRepository
     * @param SiteRepositoryInterface     $siteRepository
     * @param NodeManager                 $nodeManager
     * @param string                      $areaClass
     * @param TranslatorInterface         $translator
     */
    public function __construct(
        $nodeClass,
        TemplateRepositoryInterface $templateRepository,
        SiteRepositoryInterface $siteRepository,
        NodeManager $nodeManager,
        $areaClass,
        TranslatorInterface $translator
    ) {
        $this->nodeClass = $nodeClass;
        $this->nodeManager = $nodeManager;
        $this->templateRepository = $templateRepository;
        $this->siteRepository = $siteRepository;
        $this->areaClass = $areaClass;
        $this->translator = $translator;
        $this->schemeChoices = array(
            SchemeableInterface::SCHEME_DEFAULT => 'open_orchestra_backoffice.form.node.default_scheme',
            SchemeableInterface::SCHEME_HTTP => SchemeableInterface::SCHEME_HTTP,
            SchemeableInterface::SCHEME_HTTPS => SchemeableInterface::SCHEME_HTTPS,
            SchemeableInterface::SCHEME_FILE => SchemeableInterface::SCHEME_FILE,
            SchemeableInterface::SCHEME_FTP => SchemeableInterface::SCHEME_FTP
        );
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text', array(
                'label' => 'open_orchestra_backoffice.form.node.name',
                'attr' => array(
                    'class' => 'generate-id-source',
                )
            ))
            ->add('routePattern', 'text', array(
                'label' => 'open_orchestra_backoffice.form.node.route_pattern.name',
                'attr' => array(
                    'class' => 'generate-id-dest',
                    'help_text' => 'open_orchestra_backoffice.form.node.route_pattern.helper',
                )
            ))
            ->add('scheme', 'choice', array(
                'choices' => $this->schemeChoices,
                'label' => 'open_orchestra_backoffice.form.node.scheme'
            ))
            ->add('sitemap_changefreq', 'orchestra_frequence_choice', array(
                'label' => 'open_orchestra_backoffice.form.node.changefreq.title',
                'required' => false
            ))
            ->add('sitemap_priority', 'percent', array(
                'label' => 'open_orchestra_backoffice.form.node.priority.label',
                'type' => 'fractional',
                'precision' => 2,
                'required' => false
            ))
            ->add('theme', 'oo_theme_choice', array(
                'label' => 'open_orchestra_backoffice.form.node.theme'
            ))
            ->add('inMenu', 'checkbox', array(
                'label' => 'open_orchestra_backoffice.form.node.in_menu',
                'required' => false
            ))
            ->add('inFooter', 'checkbox', array(
                'label' => 'open_orchestra_backoffice.form.node.in_footer',
                'required' => false
            ))
            ->add('metaKeywords', 'text', array(
                'label' => 'open_orchestra_backoffice.form.website.meta_keywords',
                'required' => false,
            ))
            ->add('metaDescription', 'text', array(
                'label' => 'open_orchestra_backoffice.form.website.meta_description',
                'required' => false,
            ))
            ->add('metaIndex', 'checkbox', array(
                'label' => 'open_orchestra_backoffice.form.website.meta_index',
                'required' => false,
            ))
            ->add('metaFollow', 'checkbox', array(
                'label' => 'open_orchestra_backoffice.form.website.meta_follow',
                'required' => false,
            ))
            ->add('nodeId', 'hidden', array(
                'disabled' => true
            ))
            ->add('role', 'oo_front_role_choice', array(
                'label' => 'open_orchestra_backoffice.form.node.role',
                'required' => false,
            ))
            ->add('maxAge', 'integer', array(
                'label' => 'open_orchestra_backoffice.form.node.max_age',
                'required' => false,
            ));
        if (!array_key_exists('disabled', $options) || $options['disabled'] === false) {
            $builder->addEventSubscriber(new NodeTemplateSelectionSubscriber($this->nodeManager,$this->templateRepository));
            $builder->addEventSubscriber(new NodeThemeSelectionSubscriber($this->siteRepository));
            $builder->addEventSubscriber(new AreaCollectionSubscriber($this->areaClass, $this->translator));
        }
        if (array_key_exists('disabled', $options)) {
            $builder->setAttribute('disabled', $options['disabled']);
        }
    }

    /**
     * @param FormView      $view
     * @param FormInterface $form
     * @param array         $options
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['form_legend_helper'] = "open_orchestra_backoffice.form.node.template_selection.helper";
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => $this->nodeClass
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'oo_node';
    }
}
