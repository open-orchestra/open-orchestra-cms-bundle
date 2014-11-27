<?php

namespace PHPOrchestra\BackofficeBundle\Command;

use PHPOrchestra\ModelBundle\Document\Area;
use PHPOrchestra\ModelBundle\Model\AreaInterface;
use PHPOrchestra\ModelBundle\Model\BlockInterface;
use PHPOrchestra\ModelBundle\Model\NodeInterface;
use PHPOrchestra\ModelBundle\Model\SiteInterface;
use PHPOrchestra\ModelBundle\Repository\NodeRepository;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class OrchestraCheckConsistencyCommand
 */
class OrchestraCheckConsistencyCommand extends ContainerAwareCommand
{
    /**
     * Configure the command
     *
     * @see \Symfony\Component\Console\Command\Command::configure()
     */
    protected function configure()
    {
        $this
            ->setName('orchestra:check')
            ->setDescription('Check data base consistency')
            ->addOption(
                'nodes',
                null,
                InputOption::VALUE_NONE,
                'Check the consistency of all node.'
            );
    }

    /**
     * Execute the command
     *
     * @see \Symfony\Component\Console\Command\Command::execute()
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        $nodeRepository = $container->get('php_orchestra_model.repository.node');

        $nodes = $nodeRepository->findAll();

        if ($input->getOption('nodes')) {
            if ( false === $this->nodeConsistency($nodes)) {
                $output->writeln($container->get('translator')->trans('php_orchestra_backoffice.command.node.error'));
            } else {
                $output->writeln($container->get('translator')->trans('php_orchestra_backoffice.command.node.success'));
            }
        } else {
            $output->writeln($container->get('translator')->trans('php_orchestra_backoffice.command.empty_choices'));
        }
    }

    /**
     * @param SiteInterface  $site
     * @param NodeRepository $nodeRepository
     *
     * @return array
     */
    protected function getNodesBySiteId($site, $nodeRepository)
    {
        return $nodeRepository->findAll(array('siteId' => $site->getSiteId()));
    }

    /**
     * @param array $nodes
     *
     * @return bool
     */
    protected function nodeConsistency($nodes)
    {
        foreach ($nodes as $node) {
            if (!$this->areaConsistency($node) || !$this->blockConsistency($node)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param NodeInterface $node
     *
     * @return bool
     */
    protected function areaConsistency($node)
    {
        foreach ($node->getAreas() as $area) {
            if (!$this->checkBlockRef($area->getBlocks(), $node, $area)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param NodeInterface $node
     *
     * @return bool
     */
    protected function blockConsistency($node)
    {
        foreach ($node->getBlocks() as $block) {
            if (!$this->checkAreaRef($block->getAreas(), $node, $block)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param array          $refAreas
     * @param NodeInterface  $node
     * @param BlockInterface $block
     *
     * @return bool
     */
    protected function checkAreaRef($refAreas, $node, $block)
    {
        foreach ($refAreas as $refArea) {

            if ($refArea['nodeId'] === $node->getNodeId() || $refArea['nodeId'] === 0) {
                $result = $this->AreaIdExist($refArea['areaId'], $node->getAreas());

                if (null === $result) {
                    return false;
                } else {
                    if (!$this->checkBlock($result->getBlocks(), $block, $node)) {

                        return false;
                    }
                }
            }
        }

        return true;
    }

    /**
     * @param array         $blocks
     * @param NodeInterface $node
     * @param AreaInterface $area
     *
     * @return bool
     */
    public function checkBlockRef($blocks, $node, $area)
    {
        foreach ($blocks as $block) {
            if ($block['nodeId'] === $node->getNodeId() || $block['nodeId'] === 0) {

                if (!$this->blockIdExist($node->getBlock($block['blockId']), $area->getAreaId())) {

                    return false;
                }
            }
        }

        return true;
    }

    /**
     * @param string $areaId
     * @param array  $areas
     *
     * @return Area|null
     */
    protected function AreaIdExist($areaId, $areas)
    {
        if (!empty($areas)) {
            foreach ($areas as $area) {
                $result = $this->checkArea($areaId, $area);
                if ( null != $result) {
                    return $result;
                }
            }
        }

        return null;
    }

    /**
     * @param string        $areaId
     * @param AreaInterface $area
     *
     * @return Area|null
     */
    protected function checkArea($areaId, $area)
    {
        if ($areaId === $area->getAreaId()) {
            return $area;
        } else {
            return $this->AreaIdExist($areaId, $area->getAreas());
        }
    }

    /**
     * @param BlockInterface $block
     * @param string         $areaId
     *
     * @return bool
     */
    protected function blockIdExist($block, $areaId)
    {
        $areas = $block->getAreas();

        foreach ($areas as $area) {
            if ($area['areaId'] === $areaId) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param array          $refBlocks
     * @param BlockInterface $block
     * @param NodeInterface  $node
     *
     * @return bool
     */
    protected function checkBlock($refBlocks, $block, $node)
    {
        foreach ($refBlocks as $refBlock) {
            $blockRef = $node->getBlock($refBlock['blockId']);

            if ($blockRef->getLabel() === $block->getLabel()) {
                return true;
            }
        }

        return false;
    }
}
