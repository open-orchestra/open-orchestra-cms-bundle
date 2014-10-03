<?php

namespace PHPOrchestra\BackofficeBundle\Form\Type;

use PHPOrchestra\BackofficeBundle\EventSubscriber\AddSubmitButtonSubscriber;
use PHPOrchestra\BackofficeBundle\EventSubscriber\AreaCollectionSubscriber;
use PHPOrchestra\BackofficeBundle\EventSubscriber\NodeTypeSubscriber;
use PHPOrchestra\BackofficeBundle\EventSubscriber\TemplateChoiceSubscriber;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use PHPOrchestra\ModelBundle\Repository\TemplateRepository;

/**
 * Class NodeType
 */
class NodeType extends AbstractType
{
    protected $nodeClass;
    protected $templateRepository;

    /**
     *
     * @param string $nodeClass
     * @param TemplateRepository $templateRepository
     */
    public function __construct($nodeClass, TemplateRepository $templateRepository)
    {
        $this->nodeClass = $nodeClass;
        $this->templateRepository = $templateRepository;
    }

    /**
     *
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', 'text')
            ->add('nodeType', 'choice', array(
            'choices' => array(
                'page' => 'Page simple'
            )
        ))
            ->add('theme', 'orchestra_theme_choice')
            ->add('templateId', 'choice', array(
            'choices' => $this->getChoices()
        ))
            ->add('alias', 'text')
            ->add('language', 'orchestra_language')
            ->add('status', 'orchestra_status');

        $builder->addEventSubscriber(new AreaCollectionSubscriber());
        $builder->addEventSubscriber(new AddSubmitButtonSubscriber());
        $builder->addEventSubscriber(new TemplateChoiceSubscriber($this->templateRepository));
    }

    /**
     *
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => $this->nodeClass
        ));
    }

    /**
     *
     * @return array
     */
    protected function getChoices()
    {
        $templates = $this->templateRepository->findByDeleted(false);
        $templatesChoices = array();
        foreach ($templates as $template) {
            $templatesChoices[$template->getTemplateId()] = $template->getName();
        }

        return $templatesChoices;
    }

    /**
     *
     * @return string
     */
    public function getName()
    {
        return 'node';
    }
}
