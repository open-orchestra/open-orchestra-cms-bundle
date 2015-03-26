<?php

namespace OpenOrchestra\BackofficeBundle\Form\Type;

use OpenOrchestra\BackofficeBundle\EventSubscriber\NodeChoiceSubscriber;
use OpenOrchestra\BackofficeBundle\Manager\NodeManager;
use OpenOrchestra\BackofficeBundle\EventSubscriber\AddSubmitButtonSubscriber;
use OpenOrchestra\BackofficeBundle\EventSubscriber\AreaCollectionSubscriber;
use OpenOrchestra\BackofficeBundle\EventSubscriber\TemplateChoiceSubscriber;
use OpenOrchestra\ModelInterface\Repository\TemplateRepositoryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use OpenOrchestra\ModelInterface\Model\SchemeableInterface;

/**
 * Class NodeType
 */
class NodeType extends AbstractType
{
    protected $areaClass;
    protected $nodeClass;
    protected $nodeManager;
    protected $templateRepository;
    protected $schemeChoices;

    /**
     * @param string                      $nodeClass
     * @param TemplateRepositoryInterface $templateRepository
     * @param NodeManager                 $nodeManager
     * @param string                      $areaClass
     */
    public function __construct($nodeClass, TemplateRepositoryInterface $templateRepository, NodeManager $nodeManager, $areaClass)
    {
        $this->nodeClass = $nodeClass;
        $this->nodeManager = $nodeManager;
        $this->templateRepository = $templateRepository;
        $this->areaClass = $areaClass;
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
                'label' => 'open_orchestra_backoffice.form.node.route_pattern',
                'attr' => array(
                    'class' => 'generate-id-dest',
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
            ->add('theme', 'orchestra_theme_choice', array(
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
            ->add('role', 'orchestra_role_choice', array(
                'label' => 'open_orchestra_backoffice.form.node.role',
                'required' => false,
            ))
            ->add('maxAge', 'integer', array(
                'label' => 'open_orchestra_backoffice.form.node.max_age',
                'required' => false,
            ));
        if(!array_key_exists('disabled', $options) || $options['disabled'] == false){
            $builder->addEventSubscriber(new NodeChoiceSubscriber($this->nodeManager));
            $builder->addEventSubscriber(new TemplateChoiceSubscriber($this->templateRepository));
            $builder->addEventSubscriber(new AreaCollectionSubscriber($this->areaClass));
            $builder->addEventSubscriber(new AddSubmitButtonSubscriber());
        }
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
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
        return 'node';
    }
}
