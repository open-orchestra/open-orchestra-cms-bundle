<?php

namespace OpenOrchestra\Backoffice\Form\Type;

use OpenOrchestra\Backoffice\EventSubscriber\NodeChoiceStatusSubscriber;
use OpenOrchestra\Backoffice\EventSubscriber\NodeTemplateSelectionSubscriber;
use OpenOrchestra\Backoffice\EventSubscriber\NodeThemeSelectionSubscriber;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
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
    protected $specialPageList;
    protected $schemeChoices;
    protected $nodeChoiceStatusSubscriber;

    /**
     * @param NodeManager              $nodeManager
     * @param CurrentSiteIdInterface   $contextManager
     * @param SiteRepositoryInterface  $siteRepository
     * @param TemplateManager          $templateManager
     * @param string                   $nodeClass
     * @param array                    $specialPageList
     * @param EventSubscriberInterface $nodeChoiceStatusSubscriber
     */
    public function __construct(
        NodeManager $nodeManager,
        CurrentSiteIdInterface $contextManager,
        SiteRepositoryInterface $siteRepository,
        TemplateManager $templateManager,
        $nodeClass,
        array $specialPageList,
        EventSubscriberInterface $nodeChoiceStatusSubscriber
    ) {
        $this->nodeManager = $nodeManager;
        $this->contextManager = $contextManager;
        $this->siteRepository = $siteRepository;
        $this->templateManager = $templateManager;
        $this->nodeClass = $nodeClass;
        $this->schemeChoices = array(
            SchemeableInterface::SCHEME_DEFAULT => 'open_orchestra_backoffice.form.node.default_scheme',
            SchemeableInterface::SCHEME_HTTP => SchemeableInterface::SCHEME_HTTP,
            SchemeableInterface::SCHEME_HTTPS => SchemeableInterface::SCHEME_HTTPS,
            SchemeableInterface::SCHEME_FILE => SchemeableInterface::SCHEME_FILE,
            SchemeableInterface::SCHEME_FTP => SchemeableInterface::SCHEME_FTP
        );
        $this->specialPageList = $specialPageList;
        $this->nodeChoiceStatusSubscriber = $nodeChoiceStatusSubscriber;
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
            ->add('specialPageName', 'choice', array(
                'label' => 'open_orchestra_backoffice.form.node.specialPageName',
                'choices' => $this->specialPageList,
                'group_id' => 'properties',
                'sub_group_id' => 'properties',
                'required' => false,
            ))
            ->add('theme', 'oo_theme_choice', array(
                'label' => 'open_orchestra_backoffice.form.node.theme',
                'group_id' => 'properties',
                'sub_group_id' => 'properties',
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
            ))
            ->add('role', 'oo_front_role_choice', array(
                'label' => 'open_orchestra_backoffice.form.node.role',
                'group_id' => 'cache',
                'sub_group_id' => 'roles',
                'required' => false,
            ));

        $builder->addEventSubscriber($this->nodeChoiceStatusSubscriber);
        if (!array_key_exists('disabled', $options) || $options['disabled'] === false) {
            $builder->addEventSubscriber(new NodeThemeSelectionSubscriber($this->siteRepository));
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
        $resolver->setDefaults(array(
            'data_class' => $this->nodeClass,
            'group_enabled' => true,
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
                'roles' => array(
                    'rank' => 1,
                    'label' => 'open_orchestra_backoffice.form.node.sub_group.roles',
                ),
            ),
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
