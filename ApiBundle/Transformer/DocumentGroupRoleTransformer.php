<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use OpenOrchestra\ApiBundle\Exceptions\HttpException\RoleNotFoundHttpException;
use OpenOrchestra\ApiBundle\Exceptions\TransformerParameterTypeException;
use OpenOrchestra\Backoffice\Collector\RoleCollectorInterface;
use OpenOrchestra\BackofficeBundle\Model\DocumentGroupRoleInterface;
use OpenOrchestra\BackofficeBundle\Model\GroupInterface;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseApi\Transformer\AbstractTransformer;

/**
 * Class DocumentGroupRoleTransformer
 */
class DocumentGroupRoleTransformer extends AbstractTransformer implements TransformerWithGroupInterface
{
    protected $documentGroupRoleClass;
    protected $collector;

    /**
     * @param string                 $facadeClass
     * @param string                 $documentGroupRoleClass
     * @param RoleCollectorInterface $collector
     */
    public function __construct(
        $facadeClass,
        $documentGroupRoleClass,
        RoleCollectorInterface $collector
    ) {
        parent::__construct($facadeClass);
        $this->documentGroupRoleClass = $documentGroupRoleClass;
        $this->collector = $collector;
    }

    /**
     * @param DocumentGroupRoleInterface $documentGroupRole
     *
     * @return FacadeInterface
     *
     * @throws TransformerParameterTypeException
     */
    public function transform($documentGroupRole)
    {
        if (!$documentGroupRole instanceof DocumentGroupRoleInterface) {
            throw new TransformerParameterTypeException();
        }

        $facade = $this->newFacade();

        $facade->document = $documentGroupRole->getId();
        $facade->type = $documentGroupRole->getType();
        $facade->name = $documentGroupRole->getRole();
        $facade->accessType = $documentGroupRole->getAccessType();

        return $facade;
    }

    /**
     * @param GroupInterface                  $group
     * @param FacadeInterface                 $documentRoleFacade
     * @param DocumentGroupRoleInterface|null $source
     *
     * @throws RoleNotFoundHttpException
     * @throws TransformerParameterTypeException
     *
     * @return DocumentGroupRoleInterface
     */
    public function reverseTransformWithGroup(GroupInterface $group, FacadeInterface $documentRoleFacade, $source = null)
    {
        if (!$source instanceof DocumentGroupRoleInterface) {
            $source = new $this->documentGroupRoleClass();
        }

        if (!$this->collector->hasRole($documentRoleFacade->name)) {
            throw new RoleNotFoundHttpException();
        }

        $source->setType($documentRoleFacade->type);
        $source->setId($documentRoleFacade->document);
        $source->setRole($documentRoleFacade->name);
        $source->setAccessType($documentRoleFacade->accessType);

        if (DocumentGroupRoleInterface::ACCESS_INHERIT === $documentRoleFacade->accessType) {
            /*parent->isGranted()?*/
            $source->setGranted(true);
        } else {
            $isGranted = (DocumentGroupRoleInterface::ACCESS_GRANTED === $documentRoleFacade->accessType) ? true : false;
            $source->setGranted($isGranted);
        }

        return $source;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'document_group_role';
    }
}
