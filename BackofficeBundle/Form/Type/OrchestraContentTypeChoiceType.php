<?php

namespace PHPOrchestra\BackofficeBundle\Form\Type;

use PHPOrchestra\ModelInterface\Repository\ContentTypeRepositoryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class OrchestraContentTypeChoiceType
 */
class OrchestraContentTypeChoiceType extends AbstractType
{
    protected $contentTypeRepository;

    /**
     * @param ContentTypeRepositoryInterface $contentTypeRepository
     */
    public function __construct(ContentTypeRepositoryInterface $contentTypeRepository)
    {
        $this->contentTypeRepository = $contentTypeRepository;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'choices' => $this->getChoices()
            )
        );
    }

    /**
     * @return array
     */
    protected function getChoices()
    {
        $contentTypes = $this->contentTypeRepository->findAllByDeletedInLastVersion();

        $choices = array_map(function ($element) {
            return $element->getName();
        }, $contentTypes);

        return $choices;
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'orchestra_content_type_choice';
    }

    /**
     * @return null|string|\Symfony\Component\Form\FormTypeInterface
     */
    public function getParent()
    {
        return 'choice';
    }
}
