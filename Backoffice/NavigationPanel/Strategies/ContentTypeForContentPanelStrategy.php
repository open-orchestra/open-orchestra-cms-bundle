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
    protected $datatableParameterNames;

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
        array $defaultDatatableParameter,
        TranslatorInterface $translator,
        TranslationChoiceManager $translationChoiceManager
    )
    {
        $defaultDatatableParameter = $this->preFormatDatatableParameter($defaultDatatableParameter, $translator);
        $this->contentTypes = $contentTypeRepository->findAllNotDeletedInLastVersion($contextManager->getCurrentLocale());
        $dataParameter = $this->formatDatatableParameter($defaultDatatableParameter, $translationChoiceManager);

        parent::__construct('content_type_for_content', self::ROLE_ACCESS_CONTENT_TYPE_FOR_CONTENT, $weight, $parent, $dataParameter, null);
    }

    /**
     * @return string
     */
    public function show()
    {
        return $this->render(
            'OpenOrchestraBackofficeBundle:BackOffice:Include/NavigationPanel/Menu/Editorial/contents.html.twig',
            array(
                'contentTypes' => $this->contentTypes,
                'datatableParameterNames' => $this->datatableParameterNames,
            )
        );
    }

    /**
     * @return array
     */
    public function getDatatableParameter()
    {
        return $this->datatableParameter;
    }

    /**
     * @param array                    $defaultDatatableParameter
     * @param TranslationChoiceManager $translationChoiceManager
     */
    protected function formatDatatableParameter(array $defaultDatatableParameter, TranslationChoiceManager $translationChoiceManager)
    {
        $dataParameter = array();

        foreach ($this->contentTypes as $contentType) {
            $contentTypeId = 'contents_' . $contentType->getContentTypeId();
            $this->datatableParameterNames[$contentType->getContentTypeId()] = $contentTypeId;
            $fields = $contentType->getFields();
            $dataParameter[$contentTypeId] = $defaultDatatableParameter;
            foreach ($dataParameter[$contentTypeId] as $name => $field) {
                $dataParameter[$contentTypeId][$name]['visible'] = false;
                if (in_array($name, $contentType->getDefaultListable())) {
                    $dataParameter[$contentTypeId][$name]['visible'] = true;
                }
            }
            foreach ($fields as $field) {
                $fieldId = $field->getFieldId();
                $dataParameter[$contentTypeId][$fieldId] = array(
                    'name' => 'attributes.' . $fieldId,
                    'title' => $translationChoiceManager->choose($field->getLabels()),
                    'visible' => $field->getListable() ? true : false,
                    'activateColvis' => true,
                    'searchField' => $field->getFieldTypeSearchable(),
                );
            }
        }

        return $dataParameter;
    }
}
