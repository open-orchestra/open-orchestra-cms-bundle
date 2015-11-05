<?php

namespace OpenOrchestra\BackofficeBundle\Form\Type\Component;

use OpenOrchestra\ModelInterface\Model\ContentTypeInterface;
use OpenOrchestra\ModelInterface\Repository\ContentTypeRepositoryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ContentTypeChoiceType
 */
class ContentTypeChoiceType extends AbstractType
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
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
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
        $contentTypes = $this->contentTypeRepository->findAllNotDeletedInLastVersion();

        $choices = array_map(function (ContentTypeInterface $element) {
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
        return 'oo_content_type_choice';
    }

    /**
     * @return null|string|\Symfony\Component\Form\FormTypeInterface
     */
    public function getParent()
    {
        return 'choice';
    }
}
