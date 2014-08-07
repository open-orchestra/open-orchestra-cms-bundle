<?php

namespace PHPOrchestra\CMSBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use PHPOrchestra\CMSBundle\Model\Area;
use PHPOrchestra\CMSBundle\Form\Type\TemplateType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use PHPOrchestra\CMSBundle\Form\DataTransformer\NodeTypeTransformer;

/**
 * Class TemplateController
 */
class TemplateController extends Controller
{
    /**
     * @param Request $request
     * @param int     $templateId
     *
     * @return JsonResponse|Response
     */
    public function formAction(Request $request, $templateId = 0)
    {
        $documentManager = $this->container->get('php_orchestra_cms.document_manager');
        
        if (empty($templateId)) {
            $template = $documentManager->createDocument('Template');
            $template->setSiteId(1);
            $template->setLanguage('fr');
        } else {
            $template = $documentManager->getDocument(
                'Template',
                array('templateId' => $templateId)
            );
            $template->setVersion($template->getVersion() + 1);
        }
        
        $doSave = ($request->getMethod() == 'POST');

        if ($request->request->get('refreshRecord')) {
            $template->fromArray($request->request->all());
            $doSave = true;
        } else {
            $form = $this->createForm(
                'template',
                $template,
                array(
                    'inDialog' => true,
                    'beginJs' => array('pagegenerator/dialogNode.js', 'pagegenerator/model.js'),
                    'endJs' => array('pagegenerator/template.js?'.time()),
                    'action' => $this->getRequest()->getUri()
                )
            );
            if ($doSave) {
                $form->handleRequest($request);
                $doSave = $form->isValid();
            }
        }

        if ($doSave) {
            $response['dialog'] = $this->renderView(
                'PHPOrchestraCMSBundle:BackOffice/Dialogs:confirmation.html.twig',
                array(
                    'dialogId' => '',
                    'dialogTitle' => 'Modification du template',
                    'dialogMessage' => 'Modification ok',
                )
            );
            if (!$template->getDeleted()) {
                $template->setId(null);
                $template->setIsNew(true);
                $template->save();
            } else {
                $this->deleteTree($template->getNodeId());
                $response['redirect'] = $this->generateUrl('php_orchestra_cms_bo_edito');
            }

            return new JsonResponse($response);
        }

        return $this->render(
            'PHPOrchestraBackofficeBundle:Editorial:template.html.twig',
            array(
                'mainTitle' => 'Gestion des gabarits',
                'tableTitle' => '',
                'form' => $form->createView()
            )
        );
    }

    /**
     * Delete all version of a template
     * 
     * @param Request $request
     */
    public function deleteTree($templateId)
    {
        $documentManager = $this->get('php_orchestra_cms.document_manager');
        $templateVersions = $documentManager->getDocuments('Template', array('templateId' => $templateId));
        
        foreach ($templateVersions as $templateVersion) {
            $templateVersion->markAsDeleted();
        }
        
        return $this->render(
            'PHPOrchestraCMSBundle:BackOffice/Editorial:simpleMessage.html.twig',
            array('message' => 'Delete template process on ' . $templateId)
        );
    }

    /**
     * 
     * Get template Information
     * @param int $templateId
     * 
     */
    public function ajaxRequestAction($templateId)
    {
        $documentManager = $this->container->get('php_orchestra_cms.document_manager');
        $template = $documentManager->getDocument(
            'Template',
            array('templateId' => $templateId)
        );
        $template = $this->get('php_orchestra_cms.transformer.node_type')->transform($template);
        $result = json_decode($template->getAreas(), true);
        $result = $result['areas'];
        return new JsonResponse(
            array(
                'data' => $result
            )
        );
    }
    
}
