<?php

namespace OpenOrchestra\ApiBundle\Controller;

use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\ModelInterface\Event\RedirectionEvent;
use OpenOrchestra\ModelInterface\RedirectionEvents;
use OpenOrchestra\BaseApiBundle\Controller\Annotation as Api;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use OpenOrchestra\BaseApiBundle\Controller\BaseController;

/**
 * Class RedirectionController
 *
 * @Config\Route("redirection")
 */
class RedirectionController extends BaseController
{
    /**
     * @param int $redirectionId
     *
     * @Config\Route("/{redirectionId}", name="open_orchestra_api_redirection_show")
     * @Config\Method({"GET"})
     *
     * @Config\Security("has_role('ROLE_ACCESS_REDIRECTION')")
     *
     * @Api\Serialize()
     *
     * @return FacadeInterface
     */
    public function showAction($redirectionId)
    {
        $redirection = $this->get('open_orchestra_model.repository.redirection')->find($redirectionId);

        return $this->get('open_orchestra_api.transformer_manager')->get('redirection')->transform($redirection);
    }

    /**
     * @param Request $request
     *
     * @Config\Route("", name="open_orchestra_api_redirection_list")
     * @Config\Method({"GET"})
     *
     * @Config\Security("has_role('ROLE_ACCESS_REDIRECTION')")
     *
     * @Api\Serialize()
     *
     * @return FacadeInterface
     */
    public function listAction(Request $request)
    {
        $columns = $request->get('columns');
        $search = $request->get('search');
        $search = (null !== $search && isset($search['value'])) ? $search['value'] : null;
        $order = $request->get('order');
        $skip = $request->get('start');
        $skip = (null !== $skip) ? (int)$skip : null;
        $limit = $request->get('length');
        $limit = (null !== $limit) ? (int)$limit : null;

        $columnsNameToEntityAttribute = array(
            'site_name'     => array('key' => 'siteName'),
            'route_pattern' => array('key' => 'routePattern'),
            'locale'        => array('key' => 'locale'),
            'redirection'   => array('key' => 'url'),
            'permanent'     => array('key' => 'permanent', 'type' => 'boolean'),
        );

        $repository = $this->get('open_orchestra_model.repository.redirection');

        $redirectionCollection = $repository->findForPaginateAndSearch($columnsNameToEntityAttribute, $columns, $search, $order, $skip, $limit);
        $recordsTotal = $repository->count();
        $recordsFiltered = $repository->countFilterSearch($columnsNameToEntityAttribute, $columns, $search);

        $facade = $this->get('open_orchestra_api.transformer_manager')->get('redirection_collection')->transform($redirectionCollection);
        $facade->setRecordsTotal($recordsTotal);
        $facade->setRecordsFiltered($recordsFiltered);

        return $facade;
    }

    /**
     * @param int $redirectionId
     *
     * @Config\Route("/{redirectionId}/delete", name="open_orchestra_api_redirection_delete")
     * @Config\Method({"DELETE"})
     *
     * @Config\Security("has_role('ROLE_ACCESS_REDIRECTION')")
     *
     * @return Response
     */
    public function deleteAction($redirectionId)
    {
        $redirection = $this->get('open_orchestra_model.repository.redirection')->find($redirectionId);
        $dm = $this->get('doctrine.odm.mongodb.document_manager');
        $this->dispatchEvent(RedirectionEvents::REDIRECTION_DELETE, new RedirectionEvent($redirection));
        $dm->remove($redirection);
        $dm->flush();

        return new Response('', 200);
    }
}
