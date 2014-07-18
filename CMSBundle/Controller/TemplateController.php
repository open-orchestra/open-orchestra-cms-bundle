<?php
/**
 * This file is part of the PHPOrchestra\CMSBundle.
 *
 * @author Nicolas Anne <nicolas.anne@businessdecision.com>
 */

namespace PHPOrchestra\CMSBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use PHPOrchestra\CMSBundle\Model\Area;
use PHPOrchestra\CMSBundle\Form\Type\TemplateType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use PHPOrchestra\CMSBundle\Form\DataTransformer\NodeTypeTransformer;

class TemplateController extends Controller
{
    
    /**
     * 
     * Render the templates form
     * @param int $templateId
     * 
     */
    public function formAction($templateId = 0)
    {
        
        $request = $this->get('request');
        $documentManager = $this->container->get('phporchestra_cms.documentmanager');
        
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
        if($request->request->get('refreshRecord')){
            $template->fromArray($request->request->all());
            $doSave = true;
        }
        else{
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
            if($doSave){
                $form->handleRequest($request);
                $doSave = $form->isValid();
            }
        }
        if ($doSave) {
            $response['dialog'] = $this->render(
                'PHPOrchestraCMSBundle:BackOffice/Dialogs:confirmation.html.twig',
                array(
                    'dialogId' => '',
                    'dialogTitle' => 'Modification du template',
                    'dialogMessage' => 'Modification ok',
                )
            )>getContent();
            if(!$template->getDeleted()){
                $template->setId(null);
                $template->setIsNew(true);
                $template->save();
            }
            else{
                $this->deleteTree($template->getNodeId());
                $response['redirect'] = $this->generateUrl('php_orchestra_cms_bo_edito');
            }
            return new JsonResponse($response);
        }
        return $this->render(
            'PHPOrchestraCMSBundle:BackOffice/Editorial:template.html.twig',
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
        $documentManager = $this->get('phporchestra_cms.documentmanager');
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
        $documentManager = $this->container->get('phporchestra_cms.documentmanager');
        $template = $documentManager->getDocument(
            'Template',
            array('templateId' => $templateId)
        );
        $transformer = new NodeTypeTransformer($this->container, false);
        $template = $transformer->transform($template);
        $result = json_decode($template->getAreas(), true);
        $result = $result['areas'];
        return new JsonResponse(
            array(
                'data' => $result
            )
        );
    }
    
}
