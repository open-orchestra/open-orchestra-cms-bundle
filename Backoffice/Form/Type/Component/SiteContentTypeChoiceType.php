<?php

namespace OpenOrchestra\Backoffice\Form\Type\Component;

use OpenOrchestra\Backoffice\Context\ContextManager;
use OpenOrchestra\ModelInterface\Model\ContentTypeInterface;
use OpenOrchestra\ModelInterface\Repository\ContentTypeRepositoryInterface;
use OpenOrchestra\ModelInterface\Repository\SiteRepositoryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class SiteContentTypeChoiceType
 */
class SiteContentTypeChoiceType extends AbstractType
{
    protected $contentTypeRepository;
    protected $siteRepository;
    protected $context;

    /**
     * @param ContentTypeRepositoryInterface $contentTypeRepository
     * @param SiteRepositoryInterface        $siteRepository
     * @param ContextManager                 $context
     */
    public function __construct(
        ContentTypeRepositoryInterface $contentTypeRepository,
        SiteRepositoryInterface $siteRepository,
        ContextManager $context
    ) {
        $this->contentTypeRepository = $contentTypeRepository;
        $this->siteRepository = $siteRepository;
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
        $siteId = $this->context->getCurrentSiteId();
        $site = $this->siteRepository->findOneBySiteId($siteId);

        $currentLanguage = $this->context->getCurrentLocale();
        $contentTypes = array();
        if (!empty($site->getContentTypes())) {
            $contentTypes = $this->contentTypeRepository->findAllNotDeletedInLastVersion($site->getContentTypes());
        }
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
        return 'oo_site_content_type_choice';
    }

    /**
     * @return null|string|\Symfony\Component\Form\FormTypeInterface
     */
    public function getParent()
    {
        return 'choice';
    }
}
