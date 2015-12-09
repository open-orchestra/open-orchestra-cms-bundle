<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use OpenOrchestra\ApiBundle\Exceptions\TransformerParameterTypeException;
use OpenOrchestra\Backoffice\NavigationPanel\Strategies\AdministrationPanelStrategy;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseApi\Transformer\AbstractSecurityCheckerAwareTransformer;
use OpenOrchestra\Backoffice\Manager\TranslationChoiceManager;
use OpenOrchestra\ModelInterface\Model\ContentTypeInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Class ContentTypeTransformer
 */
class ContentTypeTransformer extends AbstractSecurityCheckerAwareTransformer
{
    protected $translationChoiceManager;

    /**
     * @param string                        $facadeClass
     * @param TranslationChoiceManager      $translationChoiceManager
     * @param AuthorizationCheckerInterface $authorizationChecker
     */
    public function __construct(
        $facadeClass,
        TranslationChoiceManager $translationChoiceManager,
        AuthorizationCheckerInterface $authorizationChecker
    ) {
        parent::__construct($facadeClass, $authorizationChecker);
        $this->translationChoiceManager = $translationChoiceManager;
    }

    /**
     * @param ContentTypeInterface $contentType
     *
     * @return FacadeInterface
     *
     * @throws TransformerParameterTypeException
     */
    public function transform($contentType)
    {
        if (!$contentType instanceof ContentTypeInterface) {
            throw new TransformerParameterTypeException();
        }

        $facade = $this->newFacade();

        $facade->id = $contentType->getId();
        $facade->contentTypeId = $contentType->getContentTypeId();
        $facade->name = $this->translationChoiceManager->choose($contentType->getNames());
        $facade->version = $contentType->getVersion();
        $facade->linkedToSite = $contentType->isLinkedToSite();

        foreach ($contentType->getFields() as $field) {
            $facade->addField($this->getTransformer('field_type')->transform($field));
        }

        $facade->addLink('_self', $this->generateRoute(
            'open_orchestra_api_content_type_show',
            array('contentTypeId' => $contentType->getContentTypeId())
        ));

        if ($this->authorizationChecker->isGranted(AdministrationPanelStrategy::ROLE_ACCESS_DELETE_CONTENT_TYPE)) {
            $facade->addLink('_self_delete', $this->generateRoute(
                'open_orchestra_api_content_type_delete',
                array('contentTypeId' => $contentType->getContentTypeId())
            ));
        }

        if ($this->authorizationChecker->isGranted(AdministrationPanelStrategy::ROLE_ACCESS_UPDATE_CONTENT_TYPE)) {
            $facade->addLink('_self_form', $this->generateRoute(
                'open_orchestra_backoffice_content_type_form',
                array('contentTypeId' => $contentType->getContentTypeId())
            ));
        }

        return $facade;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'content_type';
    }
}
