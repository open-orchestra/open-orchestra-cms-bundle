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
            return $this->get('php_orchestra_cms.display_block_manager')
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
     * @throws NotFoundHttpException
     * @return Response
     */
    public function showBackAction($nodeId, $blockId)
    {
        $node = $this->get('php_orchestra_model.repository.node')->findOneByNodeId($nodeId);

        if (null !== ($block = $node->getBlocks()->get($blockId))) {
            return $this->get('php_orchestra_cms.display_block_manager')
                ->show($node->getBlocks()->get($blockId));
        }

        throw new NotFoundHttpException();
    }

    /**
     * Render the node Block form
     *
     * @param Request $request
     * @param string  $type
     *
     * @throws \Exception
     * @return JsonResponse|Response
     */
    public function formAction(Request $request, $type)
    {
        if ($request->request->get('preview') !== null) {
            throw new \Exception("use directly the showBack method");
        }
        if ($request->request->get('refresh') !== null) {
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
