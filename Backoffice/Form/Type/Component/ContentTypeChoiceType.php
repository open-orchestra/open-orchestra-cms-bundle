<?php

namespace OpenOrchestra\Backoffice\Form\Type\Component;

use OpenOrchestra\Backoffice\Context\ContextManager;
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
    protected $context;

    /**
     * @param ContentTypeRepositoryInterface $contentTypeRepository
     * @param ContextManager                 $context
     */
    public function __construct(ContentTypeRepositoryInterface $contentTypeRepository, ContextManager $context)
    {
        $this->contentTypeRepository = $contentTypeRepository;
        $this->context = $context;
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
        $currentLanguage = $this->context->getCurrentLocale();
        $contentTypes = $this->contentTypeRepository->findAllNotDeletedInLastVersion();

        $choices = array_map(function (ContentTypeInterface $element) use ($currentLanguage) {
            return $element->getName($currentLanguage);
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
