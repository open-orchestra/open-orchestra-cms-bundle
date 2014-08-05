<?php

namespace PHPOrchestra\CMSBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class BlockController
 */
class BlockController extends Controller
{
    /**
     * Display the response linked to a block
     *
     * @param string $nodeId
     * @param string $blockId
     *
     * @throws NotFoundHttpException
     * @return Response
     */
    public function showAction($nodeId, $blockId)
    {
        $node = $this->get('php_orchestra_model.repository.node')->findOneByNodeId($nodeId);

        if (null !== ($block = $node->getBlocks()->get($blockId))) {
            return $this->get('php_orchestra_display.display_block_manager')
                ->show($node->getBlocks()->get($blockId));
        }

        throw new NotFoundHttpException();
    }

    /**
     * Display the response linked to a block
     *
     * @param string $nodeId
     * @param string $blockId
     *
     * @return Response
     */
    public function showBackAction($nodeId, $blockId)
    {
        $node = $this->get('mandango')->getRepository('Model\PHPOrchestraCMSBundle\Node')
            ->getOne($nodeId);

        $blocks = $node->getBlocks()->all();

        return $this->get('php_orchestra_display.display_block_manager')
            ->showBack($blocks[$blockId]);
    }

    /**
     * Render the node Block form
     *
     * @param string $type
     *
     * @return JsonResponse|Response
     */
    public function formAction($type)
    {
        $request = $this->get('request');

        if ($request->request->get('preview') !== null) {
            return $this->getPreview($request);
        } elseif ($request->request->get('refresh') !== null) {
            return $this->getRefresh($request, $type);
        } else {
            $refresh = array('is_node' => ($type == 'node'));
            $form = $this->createForm(
                'blocks',
                $refresh,
                array(
                    'action' => $this->generateUrl('php_orchestra_cms_blockform', array('type' => $type)),
                    'inDialog' => true,
                    'subForm' => true,
                    'beginJs' => array('pagegenerator/'.$type.'_block.js?'.time(), 'pagegenerator/dialogNode.js'),
                )
            );

            return $this->render(
                'PHPOrchestraCMSBundle:Form:form.html.twig',
                array(
                    'form' => $form->createView()
                )
            );
        }
    }

    /**
     * Get the component and attributes informations in block generate case
     *
     * @param Request $request
     *
     * @return array
     */
    protected function getGenerateInformations(Request $request)
    {
        $attributes = $request->request->all();
        $allowed=array_filter(
            array_keys($attributes),
            function ($key) {
                return preg_match('/^attributes_/', $key);
            }
        );
        $attributes = array_intersect_key($attributes, array_flip($allowed));
        $attributes = array_combine(
            array_map(
                function ($value) {
                    return preg_replace('/^attributes_/', '', $value);
                },
                array_keys($attributes)
            ),
            array_values($attributes)
        );
        $component = $request->request->get('component');

        return array($component, $attributes);
    }

    /**
     * Get the component and attributes informations in block load case
     *
     * @param Request $request
     *
     * @return array
     */
    protected function getLoadInformations(Request $request)
    {
        $component = '';
        $attributes = array();
        $block = $this->get('php_orchestra_cms.document_manager')->getBlockInNode(
            $request->request->get('nodeId'),
            $request->request->get('blockId')
        );
        if ($block) {
            $component = $block['component'];
            $attributes = $block['attributes'];
        }

        return array($component, $attributes);
    }
    
    /**
     * Render the preview of a Block
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function getPreview(Request $request)
    {
        $component = '';
        $attributes = array();
        if ($request->request->get('component') !== null) {
            list($component, $attributes) = $this->getGenerateInformations($request);
        } elseif ($request->request->get('nodeId') !== null && $request->request->get('blockId') !== null) {
            list($component, $attributes) = $this->getLoadInformations($request);
        }
        if ($component !== '') {
            $response  = $this->forward('PHPOrchestraCMSBundle:Block/'.$component.':showBack', $attributes);
            return new JsonResponse(
                array(
                    'success' => true,
                    'data' => $response->getContent()
                )
            );
        } else {
            return new JsonResponse(
                array(
                    'success' => true,
                    'data' => 'No Preview'
                )
            );
        }
    }

    /**
     * @param Request $request
     * @param mixed   $form
     * @param array   $refresh
     *
     * @return array
     */
    protected function getRecRefresh(Request $request, $form, $refresh){
        $children = $form->all();
        $restart = false;
        if(count($children) > 0){
            foreach($children as $child){
                $view = $child->createView();
                if($request->request->get($view->vars['id'])){
                    if(!array_key_exists($view->vars['name'], $refresh)){
                        $refresh[$view->vars['name']] = $request->request->get($view->vars['id']);
                        $restart = true;
                    }
                }
                else{
                    if(!array_key_exists($view->vars['name'], $refresh)){
                        $refresh[$view->vars['name']] = array();
                    }
                    list($restart, $refresh[$view->vars['name']]) = $this->getRecRefresh($request, $child, $refresh[$view->vars['name']]);
                }
                if($restart){
                    break;
                }
            }
        }
        return array($restart, $refresh);
    }
    /**
     * 
     * Render the node Block form after refresh request
     *
     * @param Request $request
     * @param mixed   $type
     *
     * @return JsonResponse
     */
    protected function getRefresh($request, $type)
    {
        $refresh = array('is_node' => ($type == 'node'));
        if(is_array($request->request->get('blocks'))){
            $refresh = array_merge($refresh, $request->request->get('blocks'));
        }
        else{
            $form = $this->createForm('blocks', $refresh);
            list($restart, $refresh) = $this->getRecRefresh($request, $form, $refresh);
            while($restart){
                $form = $this->createForm('blocks', $refresh);
                list($restart, $refresh) = $this->getRecRefresh($request, $form, $refresh);
            }
        }
        $form = $this->createForm(
            'blocks',
            $refresh,
            array(
                'action' => $this->generateUrl('php_orchestra_cms_blockform', array('type' => $type)),
                'inDialog' => false,
                'subForm' => false,
                'beginJs' => array('pagegenerator/dialogNode.js'),
            )
        );
        $render = $this->render(
            'PHPOrchestraCMSBundle:Form:form.html.twig',
            array(
                'form' => $form->createView()
            )
        );
        return new JsonResponse(
            array(
                'success' => true,
                'data' => $render->getContent()
            )
        );
    }
}
