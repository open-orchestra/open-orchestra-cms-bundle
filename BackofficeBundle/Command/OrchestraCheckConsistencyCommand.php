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
        $nodeManager = $container->get('php_orchestra_backoffice.manager.node');

        $nodes = $nodeRepository->findAll();

        if ($input->getOption('nodes')) {
            if ( false === $nodeManager->nodeConsistency($nodes)) {
                $output->writeln($container->get('translator')->trans('php_orchestra_backoffice.command.node.error'));
            } else {
                $output->writeln($container->get('translator')->trans('php_orchestra_backoffice.command.node.success'));
            }
        } else {
            $output->writeln($container->get('translator')->trans('php_orchestra_backoffice.command.empty_choices'));
        }
    }
}
