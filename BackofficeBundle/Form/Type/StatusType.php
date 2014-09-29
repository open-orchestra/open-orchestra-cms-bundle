<?php

namespace PHPOrchestra\BackofficeBundle\Form\Type;

use PHPOrchestra\BackofficeBundle\EventListener\TranslateValueInitializerListener;
use PHPOrchestra\BackofficeBundle\EventSubscriber\AddSubmitButtonSubscriber;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use PHPOrchestra\ModelBundle\Repository\ContentTypeRepository;
use PHPOrchestra\Backoffice\Manager\TranslationChoiceManager;
use Lexik\Bundle\TranslationBundle\Translation\Translator;

/**
 * Class StatusType
 */
class StatusType extends AbstractType
{
    protected $statusClass;
    protected $translateValueInitializer;
    protected $contentTypeRepository;
    protected $translationChoiceManager;
    protected $translator;
    
    /**
     * @param string                            $statusClass
     * @param TranslateValueInitializerListener $translateValueInitializer
     * @param ContentTypeRepository             $contentTypeRepository
     * @param TranslationChoiceManager          $translationChoiceManager
     * @param Translator                        $translator
     */
    public function __construct($statusClass, TranslateValueInitializerListener $translateValueInitializer, ContentTypeRepository $contentTypeRepository, TranslationChoiceManager $translationChoiceManager, Translator $translator)
    {
        $this->translateValueInitializer = $translateValueInitializer;
        $this->statusClass = $statusClass;
        $this->contentTypeRepository = $contentTypeRepository;
        $this->translationChoiceManager = $translationChoiceManager;
        $this->translator = $translator;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventListener(FormEvents::PRE_SET_DATA, array($this->translateValueInitializer, 'preSetData'));

        $builder->add('name');
        $builder->add('published', null, array('required' => false));
        $builder->add('initial', 'choice', array(
            'choices' => $this->getChoices(),
            'multiple' => true,
            'expanded' => true
        ));
        $builder->add('labels', 'translated_value_collection');
        $builder->add('role', null, array('required' => false));
        $builder->addEventSubscriber(new AddSubmitButtonSubscriber());
    }

    /**
     * @return array
     */
    protected function getChoices(){
        $contentTypes = $this->contentTypeRepository->findByDeleted(false);
        $contentTypesChoices = array();
        foreach($contentTypes as $contentType){
            $contentTypesChoices[$contentType->getContentTypeId()] = $this->translationChoiceManager->choose($contentType->getNames());
        }
        $contentTypesChoices['node'] = $this->translator->trans('php_orchestra_backoffice.left_menu.editorial.nodes');
        return $contentTypesChoices;
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'status';
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => $this->statusClass
        ));
    }

}
