<?php

namespace OpenOrchestra\Backoffice\Form\Type;

use OpenOrchestra\Backoffice\EventSubscriber\NodeTemplateSelectionSubscriber;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use OpenOrchestra\ModelInterface\Repository\SiteRepositoryInterface;
use OpenOrchestra\ModelInterface\Model\SchemeableInterface;
use OpenOrchestra\BaseBundle\Context\CurrentSiteIdInterface;
use OpenOrchestra\Backoffice\Manager\NodeManager;
use OpenOrchestra\Backoffice\Manager\TemplateManager;

/**
 * Class NodeType
 */
class NodeType extends AbstractType
{
    protected $nodeManager;
    protected $contextManager;
    protected $siteRepository;
    protected $templateManager;
    protected $nodeClass;
    protected $schemeChoices;
    protected $specialPageChoiceStatusSubscriber;
    protected $frontRoles;

    /**
     * @param NodeManager              $nodeManager
     * @param CurrentSiteIdInterface   $contextManager
     * @param SiteRepositoryInterface  $siteRepository
     * @param TemplateManager          $templateManager
     * @param string                   $nodeClass
     * @param EventSubscriberInterface $specialPageChoiceStatusSubscriber
     * @param array                    $frontRoles
     */
    public function __construct(
        NodeManager $nodeManager,
        CurrentSiteIdInterface $contextManager,
        SiteRepositoryInterface $siteRepository,
        TemplateManager $templateManager,
        $nodeClass,
        EventSubscriberInterface $specialPageChoiceStatusSubscriber,
        array $frontRoles
    ) {
        $this->nodeManager = $nodeManager;
        $this->contextManager = $contextManager;
        $this->siteRepository = $siteRepository;
        $this->templateManager = $templateManager;
        $this->nodeClass = $nodeClass;
        $this->schemeChoices = array(
            SchemeableInterface::SCHEME_DEFAULT => 'open_orchestra_backoffice.form.node.default_scheme',
            SchemeableInterface::SCHEME_HTTP => SchemeableInterface::SCHEME_HTTP,
            SchemeableInterface::SCHEME_HTTPS => SchemeableInterface::SCHEME_HTTPS
        );
        $this->specialPageChoiceStatusSubscriber = $specialPageChoiceStatusSubscriber;
        $this->frontRoles = $frontRoles;
   }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nodeId', 'hidden', array(
                'disabled' => true,
                'group_id' => 'properties',
                'sub_group_id' => 'properties',
            ))
            ->add('name', 'text', array(
                'label' => 'open_orchestra_backoffice.form.node.title',
                'group_id' => 'properties',
                'sub_group_id' => 'properties',
                'attr' => array(
                    'class' => 'generate-id-source',
                )
            ))
            ->add('routePattern', 'text', array(
                'label' => 'open_orchestra_backoffice.form.node.route_pattern.name',
                'group_id' => 'properties',
                'sub_group_id' => 'properties',
                'attr' => array(
                    'class' => 'generate-id-dest',
                    'help_text' => 'open_orchestra_backoffice.form.node.route_pattern.helper',
                )
            ))
            ->add('scheme', 'choice', array(
                'choices' => $this->schemeChoices,
                'group_id' => 'properties',
                'sub_group_id' => 'properties',
                'label' => 'open_orchestra_backoffice.form.node.scheme'
            ))
            ->add('inMenu', 'checkbox', array(
                'label' => 'open_orchestra_backoffice.form.node.in_menu',
                'group_id' => 'properties',
                'sub_group_id' => 'properties',
                'required' => false
            ))
            ->add('inFooter', 'checkbox', array(
                'label' => 'open_orchestra_backoffice.form.node.in_footer',
                'group_id' => 'properties',
                'sub_group_id' => 'properties',
                'required' => false
            ))
            ->add('publishDate', 'oo_date_picker', array(
                'widget' => 'single_text',
                'label' => 'open_orchestra_backoffice.form.node.publish_date',
                'group_id' => 'properties',
                'sub_group_id' => 'publication',
                'required' => false
            ))
            ->add('unpublishDate', 'oo_date_picker', array(
                'widget' => 'single_text',
                'label' => 'open_orchestra_backoffice.form.node.unpublish_date',
                'group_id' => 'properties',
                'sub_group_id' => 'publication',
                'required' => false
            ))

            ->add('seoTitle', 'text', array(
                'label' => 'open_orchestra_backoffice.form.node.seo_title',
                'group_id' => 'seo',
                'sub_group_id' => 'seo',
                'required' => false,
            ))
            ->add('metaDescription', 'textarea', array(
                'label' => 'open_orchestra_backoffice.form.node.meta_description',
                'group_id' => 'seo',
                'sub_group_id' => 'seo',
                'required' => false,
            ))
            ->add('metaIndex', 'checkbox', array(
                'label' => 'open_orchestra_backoffice.form.node.meta_index',
                'group_id' => 'seo',
                'sub_group_id' => 'seo',
                'required' => false,
            ))
            ->add('metaFollow', 'checkbox', array(
                'label' => 'open_orchestra_backoffice.form.node.meta_follow',
                'group_id' => 'seo',
                'sub_group_id' => 'seo',
                'required' => false,
            ))
            ->add('sitemap_changefreq', 'orchestra_frequence_choice', array(
                'label' => 'open_orchestra_backoffice.form.node.changefreq.title',
                'group_id' => 'seo',
                'sub_group_id' => 'seo',
                'required' => false
            ))
            ->add('sitemap_priority', 'percent', array(
                'label' => 'open_orchestra_backoffice.form.node.priority.label',
                'type' => 'fractional',
                'precision' => 2,
                'group_id' => 'seo',
                'sub_group_id' => 'seo',
                'required' => false
            ))
            ->add('canonicalPage', 'oo_node_choice', array(
                'label' => 'open_orchestra_backoffice.form.node.canonical',
                'group_id' => 'seo',
                'sub_group_id' => 'canonical',
                'required' => false
            ))

            ->add('keywords', 'oo_keywords_choice', array(
                'label' => 'open_orchestra_backoffice.form.node.keywords',
                'group_id' => 'keywords',
                'sub_group_id' => 'keywords',
                'required' => false
            ))

            ->add('maxAge', 'integer', array(
                'label' => 'open_orchestra_backoffice.form.node.max_age',
                'group_id' => 'cache',
                'sub_group_id' => 'cache',
                'required' => false,
            ));

            if (!empty($this->frontRoles)) {
                $builder->add('frontRoles', 'choice', array(
                    'label' => false,
                    'choices' => array_flip($this->frontRoles),
                    'choices_as_values' => true,
                    'choice_label' => function ($value, $key) {
                        return 'open_orchestra_backoffice.form.role.' . $key;
                    },
                    'multiple' => true,
                    'expanded' => true,
                    'group_id' => 'cache',
                    'sub_group_id' => 'access',
                    'required' => false
                ));
            }

        $builder->addEventSubscriber($this->specialPageChoiceStatusSubscriber);
        if (!array_key_exists('disabled', $options) || $options['disabled'] === false) {
            $builder->addEventSubscriber(new NodeTemplateSelectionSubscriber(
                $this->nodeManager,
                $this->contextManager,
                $this->siteRepository,
                $this->templateManager
            ));
        }
        if (array_key_exists('disabled', $options)) {
            $builder->setAttribute('disabled', $options['disabled']);
        }
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $subGroupRender = array(
            'properties' => array(
                'rank' => 0,
                'label' => 'open_orchestra_backoffice.form.node.sub_group.properties',
            ),
            'style' => array(
                'rank' => 1,
                'label' => 'open_orchestra_backoffice.form.node.sub_group.style',
            ),
            'publication' => array(
                'rank' => 2,
                'label' => 'open_orchestra_backoffice.form.node.sub_group.publication',
            ),
            'seo' => array(
                'rank' => 0,
                'label' => 'open_orchestra_backoffice.form.node.sub_group.seo',
            ),
            'canonical' => array(
                'rank' => 1,
                'label' => 'open_orchestra_backoffice.form.node.sub_group.canonical',
            ),
            'keywords' => array(
                'rank' => 1,
                'label' => 'open_orchestra_backoffice.form.node.sub_group.keywords',
            ),
            'cache' => array(
                'rank' => 0,
                'label' => 'open_orchestra_backoffice.form.node.sub_group.cache',
            )
        );
        if (!empty($this->frontRoles)) {
            $subGroupRender['access'] = array(
                'rank' => 1,
                'label' => 'open_orchestra_backoffice.form.node.sub_group.access',
            );
        }

        $resolver->setDefaults(array(
            'data_class' => $this->nodeClass,
            'group_enabled' => true,
            'delete_button' => false,
            'group_render' => array(
                'properties' => array(
                    'rank' => 0,
                    'label' => 'open_orchestra_backoffice.form.node.group.properties',
                ),
                'seo' => array(
                    'rank' => 1,
                    'label' => 'open_orchestra_backoffice.form.node.group.seo',
                ),
                'keywords' => array(
                    'rank' => 2,
                    'label' => 'open_orchestra_backoffice.form.node.group.keywords',
                ),
                'cache' => array(
                    'rank' => 3,
                    'label' => 'open_orchestra_backoffice.form.node.group.cache',
                ),
            ),
            'sub_group_render' => array(
                'properties' => array(
                    'rank' => 0,
                    'label' => 'open_orchestra_backoffice.form.node.sub_group.properties',
                ),
                'style' => array(
                    'rank' => 1,
                    'label' => 'open_orchestra_backoffice.form.node.sub_group.style',
                ),
                'publication' => array(
                    'rank' => 2,
                    'label' => 'open_orchestra_backoffice.form.node.sub_group.publication',
                ),
                'seo' => array(
                    'rank' => 0,
                    'label' => 'open_orchestra_backoffice.form.node.sub_group.seo',
                ),
                'canonical' => array(
                    'rank' => 1,
                    'label' => 'open_orchestra_backoffice.form.node.sub_group.canonical',
                ),
                'keywords' => array(
                    'rank' => 1,
                    'label' => 'open_orchestra_backoffice.form.node.sub_group.keywords',
                ),
                'cache' => array(
                    'rank' => 0,
                    'label' => 'open_orchestra_backoffice.form.node.sub_group.cache',
                ),
                'access' => array(
                    'rank' => 1,
                    'label' => 'open_orchestra_backoffice.form.node.sub_group.access',
                )
            ),
        ));
    }

    /**
     * @param FormView      $view
     * @param FormInterface $form
     * @param array         $options
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        parent::buildView($view, $form, $options);
        $view->vars['delete_button'] = $options['delete_button'];
    }


    /**
     * @return string
     */
    public function getName()
    {
        return 'oo_node';
    }
}
