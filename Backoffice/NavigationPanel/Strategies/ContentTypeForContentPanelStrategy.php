<?php

namespace OpenOrchestra\Backoffice\NavigationPanel\Strategies;

use OpenOrchestra\ModelInterface\Repository\ContentTypeRepositoryInterface;
use OpenOrchestra\Backoffice\Context\ContextManager;
use OpenOrchestra\Backoffice\Manager\TranslationChoiceManager;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class ContentTypeForContentPanel
 */
class ContentTypeForContentPanelStrategy extends AbstractNavigationPanelStrategy
{
    const ROLE_ACCESS_CONTENT_TYPE_FOR_CONTENT = 'ROLE_ACCESS_CONTENT_TYPE_FOR_CONTENT';
    const ROLE_ACCESS_CREATE_CONTENT_TYPE_FOR_CONTENT = 'ROLE_ACCESS_CREATE_CONTENT_TYPE_FOR_CONTENT';
    const ROLE_ACCESS_UPDATE_CONTENT_TYPE_FOR_CONTENT = 'ROLE_ACCESS_UPDATE_CONTENT_TYPE_FOR_CONTENT';
    const ROLE_ACCESS_DELETE_CONTENT_TYPE_FOR_CONTENT = 'ROLE_ACCESS_DELETE_CONTENT_TYPE_FOR_CONTENT';

    protected $contentTypes;
    protected $translationChoiceManager;

    /**
     * @param ContentTypeRepositoryInterface $contentTypeRepository
     * @param ContextManager                 $contextManager
     * @param string                         $parent
     * @param int                            $weight
     * @param array                          $defaultDatatableParameter
     * @param TranslatorInterface            $translator
     * @param TranslationChoiceManager       $translationChoiceManager
     */
    public function __construct(
        ContentTypeRepositoryInterface $contentTypeRepository,
        ContextManager $contextManager,
        $parent,
        $weight,
        array $dataParameter,
        TranslatorInterface $translator,
        TranslationChoiceManager $translationChoiceManager
    ) {
        $this->contentTypes = $contentTypeRepository->findAllNotDeletedInLastVersion($contextManager->getCurrentLocale());
        $this->translationChoiceManager = $translationChoiceManager;

        parent::__construct('content_type_for_content', self::ROLE_ACCESS_CONTENT_TYPE_FOR_CONTENT, $weight, $parent, $dataParameter, $translator);
    }

    /**
     * @return string
     */
    public function show()
    {
        $datatableParameterNames = array();
        foreach ($this->contentTypes as $contentType) {
            $datatableParameterNames[] = 'contents_' . $contentType->getContentTypeId();
        }

        return $this->render(
            'OpenOrchestraBackofficeBundle:BackOffice:Include/NavigationPanel/Menu/Editorial/contents.html.twig',
            array(
                'contentTypes' => $this->contentTypes,
                'datatableParameterNames' => $datatableParameterNames,
            )
        );
    }

    /**
     * @return array
     */
    public function getDatatableParameter()
    {
       if ($this->translator instanceof TranslatorInterface) {
            $this->datatableParameter = $this->preFormatDatatableParameter($this->datatableParameter, $this->translator);
        }

        $dataParameter = array();

        foreach ($this->contentTypes as $contentType) {
            $contentTypeId = 'contents_' . $contentType->getContentTypeId();
            $dataParameter[$contentTypeId] = $this->datatableParameter;
            $defaultListable = $contentType->getDefaultListable();
            foreach ($dataParameter[$contentTypeId] as &$parameter) {
                $name = $parameter['name'];
                $parameter['visible'] = in_array($name, $defaultListable) && $defaultListable[$name];
            }
            $fields = $contentType->getFields();
            foreach ($fields as $field) {
                $dataParameter[$contentTypeId][] = array(
                    'name' => 'attributes.' . $field->getFieldId() . '.string_value',
                    'title' => $this->translationChoiceManager->choose($field->getLabels()),
                    'visible' => $field->getListable() === true,
                    'activateColvis' => true,
                    'searchField' => $field->getFieldTypeSearchable(),
                );
            }
        }

        return $dataParameter;
    }
}
