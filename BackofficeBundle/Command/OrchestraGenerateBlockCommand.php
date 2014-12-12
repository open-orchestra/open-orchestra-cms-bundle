<?php

namespace PHPOrchestra\BackofficeBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class OrchestraGenerateBlockCommand
 */
class OrchestraGenerateBlockCommand extends ContainerAwareCommand
{
    /**
     * Configure the command
     */
    protected function configure()
    {
        $this->setName('orchestra:generate:block');
        $this->setDefinition(array(
            new InputOption('block-name', '', InputOption::VALUE_REQUIRED, 'The name of the block to create'),
            new InputOption('form-generator-dir', '', InputOption::VALUE_REQUIRED, 'The directory where to create the generator strategy'),
            new InputOption('form-generator-conf', '', InputOption::VALUE_OPTIONAL, 'The file where to store the generator configuration'),
            new InputOption('front-display-dir', '', InputOption::VALUE_REQUIRED, 'The directory where to create the front display strategy'),
            new InputOption('front-display-conf', '', InputOption::VALUE_OPTIONAL, 'The file where to store the front display configuration'),
            new InputOption('backoffice-icon-dir', '', InputOption::VALUE_REQUIRED, 'The directory where to store the backoffice icon strategy'),
            new InputOption('backoffice-icon-conf', '', InputOption::VALUE_OPTIONAL, 'The file where to store the backoffice icon configuration'),
            new InputOption('backoffice-display-dir', '', InputOption::VALUE_REQUIRED, 'The directory where to store the backoffice display strategy'),
            new InputOption('backoffice-display-conf', '', InputOption::VALUE_OPTIONAL, 'The file where to store the backoffice display configuration'),
        ));
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln($input->getOptions());
    }
}
