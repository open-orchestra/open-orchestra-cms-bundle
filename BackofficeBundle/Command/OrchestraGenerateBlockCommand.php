<?php

namespace PHPOrchestra\BackofficeBundle\Command;

use PHPOrchestra\Backoffice\Generator\BlockGenerator;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

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
            new InputOption('form-generator-conf', '', InputOption::VALUE_OPTIONAL, 'The file where to store the generator configuration', 'generator.yml'),
            new InputOption('form-generator-namespace', '', InputOption::VALUE_OPTIONAL, 'The namespaces for the form generator', 'PHPOrchestra\Backoffice'),
            new InputOption('front-display-dir', '', InputOption::VALUE_REQUIRED, 'The directory where to create the front display strategy'),
            new InputOption('front-display-conf', '', InputOption::VALUE_OPTIONAL, 'The file where to store the front display configuration', 'display.yml'),
            new InputOption('front-display-namespace', '', InputOption::VALUE_OPTIONAL, 'The namespaces for the front display', 'PHPOrchestra\DisplayBundle'),
            new InputOption('backoffice-icon-dir', '', InputOption::VALUE_REQUIRED, 'The directory where to store the backoffice icon strategy'),
            new InputOption('backoffice-icon-conf', '', InputOption::VALUE_OPTIONAL, 'The file where to store the backoffice icon configuration', 'icon.yml'),
            new InputOption('backoffice-icon-namespace', '', InputOption::VALUE_OPTIONAL, 'The namespace for the backoffice icon', 'PHPOrchestra\BackofficeBundle'),
            new InputOption('backoffice-display-dir', '', InputOption::VALUE_REQUIRED, 'The directory where to store the backoffice display strategy'),
            new InputOption('backoffice-display-conf', '', InputOption::VALUE_OPTIONAL, 'The file where to store the backoffice display configuration', 'display.yml'),
            new InputOption('backoffice-display-namespace', '', InputOption::VALUE_OPTIONAL, 'The namespace for the backoffice display', 'PHPOrchestra\BackofficeBundle'),
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
        $generator = new BlockGenerator($this->getContainer()->getParameter('kernel.root_dir') . '/..');
        $generator->setSkeletonDirs(array(
            __DIR__ . '/../Resources/skeleton',
            __DIR__ . '/../../../../phporchestra-display-bundle/PHPOrchestra/DisplayBundle/Resources/skeleton',
        ));

        $generator->generate(
            $input->getOption('block-name'),
            $input->getOption('form-generator-dir'),
            $input->getOption('form-generator-namespace'),
            $input->getOption('front-display-dir'),
            $input->getOption('front-display-namespace'),
            $input->getOption('backoffice-icon-dir'),
            $input->getOption('backoffice-icon-namespace'),
            $input->getOption('backoffice-display-dir'),
            $input->getOption('backoffice-display-namespace')
        );
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        /** @var QuestionHelper $helper */
        $helper = $this->getHelper('question');

        $answer = '';
        while('' == $answer) {
            $question = new Question('Block name :');
            $answer = $helper->ask($input, $output, $question);
            $input->setOption('block-name', $answer);
        }

        $answer = '';
        while('' == $answer) {
            $question = new Question('Generator dir :');
            $answer = $helper->ask($input, $output, $question);
            $input->setOption('form-generator-dir', $answer);
        }
        $question = new Question('Generator conf :', $input->getOption('form-generator-conf'));
        $answer = $helper->ask($input, $output, $question);
        $input->setOption('form-generator-conf', $answer);
        $question = new Question('Generator namespace :', $input->getOption('form-generator-namespace'));
        $answer = $helper->ask($input, $output, $question);
        $input->setOption('form-generator-namespace', $answer);

        $answer = '';
        while('' == $answer) {
            $question = new Question('Front display dir :');
            $answer = $helper->ask($input, $output, $question);
            $input->setOption('front-display-dir', $answer);
        }
        $question = new Question('Front display conf :', $input->getOption('front-display-conf'));
        $answer = $helper->ask($input, $output, $question);
        $input->setOption('front-display-conf', $answer);
        $question = new Question('Front display namespace :', $input->getOption('front-display-namespace'));
        $answer = $helper->ask($input, $output, $question);
        $input->setOption('front-display-namespace', $answer);

        $answer = '';
        while('' == $answer) {
            $question = new Question('Backoffice icon dir :');
            $answer = $helper->ask($input, $output, $question);
            $input->setOption('backoffice-icon-dir', $answer);
        }
        $question = new Question('Backoffice icon conf :', $input->getOption('backoffice-icon-conf'));
        $answer = $helper->ask($input, $output, $question);
        $input->setOption('backoffice-icon-conf', $answer);
        $question = new Question('Backoffice icon namespace :', $input->getOption('backoffice-icon-namespace'));
        $answer = $helper->ask($input, $output, $question);
        $input->setOption('backoffice-icon-namespace', $answer);

        $answer = '';
        while('' == $answer) {
            $question = new Question('Backoffice display dir :');
            $answer = $helper->ask($input, $output, $question);
            $input->setOption('backoffice-display-dir', $answer);
        }
        $question = new Question('Backoffice display conf :', $input->getOption('backoffice-display-conf'));
        $answer = $helper->ask($input, $output, $question);
        $input->setOption('backoffice-display-conf', $answer);
        $question = new Question('Backoffice display namespace :', $input->getOption('backoffice-display-namespace'));
        $answer = $helper->ask($input, $output, $question);
        $input->setOption('backoffice-display-namespace', $answer);
    }


}
