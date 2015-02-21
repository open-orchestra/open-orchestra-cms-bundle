<?php

namespace OpenOrchestra\BackofficeBundle\Command;

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
        $nodeRepository = $container->get('open_orchestra_model.repository.node');
        $nodeManager = $container->get('open_orchestra_backoffice.manager.node');

        $nodes = $nodeRepository->findAll();

        $message = 'empty_choices';
        if ($input->getOption('nodes')) {
            $message = 'node.success';
            if ( false === $nodeManager->nodeConsistency($nodes)) {
                $message = 'node.error';
            }
        }

        $output->writeln($container->get('translator')->trans('open_orchestra_backoffice.command.' . $message));
    }
}
