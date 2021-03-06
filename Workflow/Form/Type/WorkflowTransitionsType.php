<?php

namespace OpenOrchestra\Workflow\Form\Type;

use OpenOrchestra\Backoffice\Context\ContextBackOfficeInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use OpenOrchestra\ModelInterface\Repository\StatusRepositoryInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class WorkflowTransitionsType
 */
class WorkflowTransitionsType extends AbstractType
{
    protected $statuses;
    protected $locale;

    /**
     * @param StatusRepositoryInterface  $statusRepository
     * @param ContextBackOfficeInterface $contextManager
     */
    public function __construct(StatusRepositoryInterface $statusRepository, ContextBackOfficeInterface $contextManager)
    {
        $this->locale = $contextManager->getBackOfficeLanguage();
        $this->statuses = $statusRepository->findNotOutOfWorkflow(array('labels.' . $this->locale => 1));
    }

    /**
     * Configures the options for this type.
     *
     * @param OptionsResolver $resolver The resolver for the options
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'type'         => 'oo_workflow_profile_transitions',
                'allow_add'    => false,
                'allow_delete' => false,
                'options'      => array(
                    'statuses' => $this->statuses,
                    'locale'       => $this->locale
                )
            )
        );
    }

    /**
     * @param FormView      $view
     * @param FormInterface $form
     * @param array         $options
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        foreach ($this->statuses as $status) {
            $view->vars['statuses'][$status->getId()] = $status->getLabel($this->locale);
        }
    }

    /**
     * return string
     */
    public function getParent()
    {
        return 'collection';
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'oo_workflow_transitions';
    }
}
