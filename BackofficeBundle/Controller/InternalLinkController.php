<?php

namespace OpenOrchestra\BackofficeBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * Class InternalLinkController
 */
class InternalLinkController extends AbstractAdminController
{
    /**
     * @Config\Route("/internal_link/form", name="open_orchestra_backoffice_internal_link_form")
     *
     * @return Response
     */
    public function formAction(Request $request)
    {
        $option = array('action' => $this->generateUrl('open_orchestra_backoffice_internal_link_form'));

        $option["method"] = "POST";
        if ("PATCH" === $request->getMethod()) {
            $option["validation_groups"] = false;
            $option["method"] = "PATCH";
        }
        $form = $this->createForm(
            'oo_internal_link',
            $request->query->all(),
            $option
        );

        $form->handleRequest($request);

        return $this->renderAdminForm($form);
    }
}
