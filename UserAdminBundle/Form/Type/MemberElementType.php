<?php

namespace OpenOrchestra\UserAdminBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use OpenOrchestra\UserBundle\Repository\UserRepositoryInterface;

/**
 * Class MemberElementType
 */
class MemberElementType extends AbstractType
{
    protected $userRepository;

    /**
     * @param UserRepositoryInterface $userRepository
     */
    public function __construct(
        UserRepositoryInterface $userRepository
    ) {
        $this->userRepository = $userRepository;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $userId = '__id__';
        if (!is_null($options['property_path'])) {
            $userId = preg_replace('/^\[(.*)\]$/', '$1', $options['property_path']);
        }

        $builder->add('member', 'radio', array(
            'label' => false,
            'value' => $userId,
            'data' => true,
        ));
    }

    /**
     * @param FormView      $view
     * @param FormInterface $form
     * @param array         $options
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['checked'] = true;
        $userId = $view->vars['name'];
        $user = $this->userRepository->find($userId);

        if (!is_null($user)) {
            $view->vars['parameters'] = array(
                'id' => $user->getId(),
                'firstName' => $user->getFirstName(),
                'lastName' => $user->getLastName(),
                'email' => $user->getEmail(),
            );
        }
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'oo_member_element';
    }
}
