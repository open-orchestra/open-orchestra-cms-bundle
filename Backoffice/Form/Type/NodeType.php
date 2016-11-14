<?php

namespace OpenOrchestra\Backoffice\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use OpenOrchestra\Backoffice\EventSubscriber\NodeThemeSelectionSubscriber;
use OpenOrchestra\ModelInterface\Repository\SiteRepositoryInterface;
use OpenOrchestra\ModelInterface\Model\SchemeableInterface;
use OpenOrchestra\BaseBundle\Context\CurrentSiteIdInterface;
use OpenOrchestra\Backoffice\EventSubscriber\NodeTemplateSelectionSubscriber;
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

    /**
     * @param NodeManager             $nodeManager
     * @param CurrentSiteIdInterface  $contextManager
     * @param SiteRepositoryInterface $siteRepository
     * @param TemplateManager         $templateManager
     * @param string                  $nodeClass
     * @param array $specialPageList
     */
    public function __construct(
        NodeManager $nodeManager,
        CurrentSiteIdInterface $contextManager,
        SiteRepositoryInterface $siteRepository,
        TemplateManager $templateManager,
        $nodeClass,
        array $specialPageList
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
            ->add('specialPageName', 'choice', array(
                'label' => 'open_orchestra_backoffice.form.node.specialPageName',
                'choices' => $this->specialPageList,
                'required' => false
            ));
        if (true === $options['activateBoLabel']) {
            $builder->add('boLabel', 'text', array(
                'label' => 'open_orchestra_backoffice.form.node.boLabel.name',
                'attr' => array(
                    'class' => 'generate-id-dest',
                    'help_text' => 'open_orchestra_backoffice.form.node.boLabel.helper',
                )
            ));
        }

        $builder->add('routePattern', 'text', array(
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
            ->add('publishDate', 'date', array(
                'widget' => 'single_text',
                'label' => 'open_orchestra_backoffice.form.node.publish_date',
                'required' => false
            ))
            ->add('unpublishDate', 'date', array(
                'widget' => 'single_text',
                'label' => 'open_orchestra_backoffice.form.node.unpublish_date',
                'required' => false
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
            'activateBoLabel' => true,
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
