<?php

namespace OpenOrchestra\ApiBundle\Controller;
use OpenOrchestra\ApiBundle\Controller\Annotation as Api;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class TranslateController
 *
 * @Config\Route("translate")
 */
class TranslateController extends BaseController
{
    /**
     * @param Request $request
     *
     * @Config\Route("/table", name="open_orchestra_api_translate")
     * @Config\Method({"GET"})
     * @Api\Serialize()
     *
     * @Config\Security("has_role('ROLE_ACCESS_TREE_NODE')")
     *
     */
    public function translateAction(Request $request)
    {
        $displayedElements = $request->get('displayedElements');
        foreach($displayedElements as &$displayedElement){
            $displayedElement = $this->get('translator')->trans('open_orchestra_backoffice.table.'.$request->get('entityType').'.'.$displayedElement);
        }
        return $displayedElements;
    }

}
