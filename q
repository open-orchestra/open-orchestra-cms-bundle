[1mdiff --git a/BackofficeBundle/Controller/BlockController.php b/BackofficeBundle/Controller/BlockController.php[m
[1mindex 4eb8774..eb59c4f 100644[m
[1m--- a/BackofficeBundle/Controller/BlockController.php[m
[1m+++ b/BackofficeBundle/Controller/BlockController.php[m
[36m@@ -2,7 +2,6 @@[m
 [m
 namespace OpenOrchestra\BackofficeBundle\Controller;[m
 [m
[31m-use OpenOrchestra\Backoffice\NavigationPanel\Strategies\GeneralNodesPanelStrategy;[m
 use OpenOrchestra\Backoffice\NavigationPanel\Strategies\TreeNodesPanelStrategy;[m
 use OpenOrchestra\ModelInterface\Event\NodeEvent;[m
 use OpenOrchestra\ModelInterface\Model\NodeInterface;[m
[36m@@ -24,7 +23,7 @@[m [mclass BlockController extends AbstractAdminController[m
      * @Config\Route("/block/form/{nodeId}/{blockNumber}", name="open_orchestra_backoffice_block_form", requirements={"blockNumber" = "\d+"}, defaults={"blockNumber" = 0})[m
      * @Config\Method({"GET", "POST"})[m
      *[m
[31m-     * @Config\Security("is_granted('ROLE_ACCESS_UPDATE_NODE') or is_granted('ROLE_ACCESS_UPDATE_GENERAL_NODE')")[m
[32m+[m[32m     * @Config\Security("is_granted('ROLE_ACCESS_UPDATE_NODE') or is_granted('ROLE_ACCESS_UPDATE_ERROR_NODE')")[m
      *[m
      * @return Response[m
      */[m
[36m@@ -41,7 +40,7 @@[m [mclass BlockController extends AbstractAdminController[m
         );[m
 [m
         if ($node) {[m
[31m-            $editionRole = $node->getNodeType() === NodeInterface::TYPE_TRANSVERSE? GeneralNodesPanelStrategy::ROLE_ACCESS_UPDATE_GENERAL_NODE:TreeNodesPanelStrategy::ROLE_ACCESS_UPDATE_NODE;[m
[32m+[m[32m            $editionRole = $node->getNodeType() === NodeInterface::TYPE_ERROR? TreeNodesPanelStrategy::ROLE_ACCESS_UPDATE_ERROR_NODE:TreeNodesPanelStrategy::ROLE_ACCESS_UPDATE_NODE;[m
             $options['disabled'] = !$this->get('security.authorization_checker')->isGranted($editionRole, $node);[m
         }[m
         $form = parent::createForm('oo_block', $block, $options);[m
[1mdiff --git a/BackofficeBundle/Resources/views/Tree/tree.html.twig b/BackofficeBundle/Resources/views/Tree/tree.html.twig[m
[1mindex 70ac25b..d223191 100644[m
[1m--- a/BackofficeBundle/Resources/views/Tree/tree.html.twig[m
[1m+++ b/BackofficeBundle/Resources/views/Tree/tree.html.twig[m
[36m@@ -1,13 +1,19 @@[m
 {% import "OpenOrchestraBackofficeBundle:Tree:tree-macros.html.twig" as macros %}[m
[31m-<li>[m
[31m-    <a href="#">[m
[31m-        <span class="menu-item-parent">[m
[31m-            {{ name }}[m
[31m-        </span>[m
[31m-    </a>[m
[32m+[m[32m{% set tree %}[m
[32m+[m[32m    <li>[m
[32m+[m[32m        <a href="#">[m
[32m+[m[32m            <span class="menu-item-parent">[m
[32m+[m[32m                {{ name }}[m
[32m+[m[32m            </span>[m
[32m+[m[32m        </a>[m
 [m
[31m-    <ul class="{{ name }}-connectedSortable">[m
[31m-        {% block menu %}[m
[31m-        {% endblock %}[m
[31m-    </ul>[m
[31m-</li>[m
\ No newline at end of file[m
[32m+[m[32m        <ul class="{{ name }}-connectedSortable">[m
[32m+[m[32m            {% block menu %}[m
[32m+[m[32m            {% endblock %}[m
[32m+[m[32m        </ul>[m
[32m+[m[32m    </li>[m
[32m+[m[32m{% endset %}[m
[32m+[m
[32m+[m[32m{% if not tree is empty and not block('menu')|trim is empty%}[m
[32m+[m[32m    {{ tree|raw }}[m
[32m+[m[32m{% endif %}[m
\ No newline at end of file[m
[1mdiff --git a/UserAdminBundle/Resources/views/AdministrationPanel/user.html.twig b/UserAdminBundle/Resources/views/AdministrationPanel/user.html.twig[m
[1mindex 254c965..76ff1df 100644[m
[1m--- a/UserAdminBundle/Resources/views/AdministrationPanel/user.html.twig[m
[1m+++ b/UserAdminBundle/Resources/views/AdministrationPanel/user.html.twig[m
[36m@@ -9,4 +9,4 @@[m
             >[m
         {{ 'open_orchestra_user_admin.left_menu.administration.user.list'|trans }}[m
     </a>[m
[31m-<li>[m
[32m+[m[32m</li>[m
