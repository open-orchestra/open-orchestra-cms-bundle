<?php

namespace PHPOrchestra\LogBundle\Transformer;

use PHPOrchestra\ApiBundle\Facade\FacadeInterface;
use PHPOrchestra\ApiBundle\Transformer\AbstractTransformer;
use PHPOrchestra\LogBundle\Facade\LogFacade;
use PHPOrchestra\LogBundle\Model\LogInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class LogTransformer
 */
class LogTransformer extends AbstractTransformer
{
    protected $translator;

    /**
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @param LogInterface $mixed
     *
     * @return FacadeInterface
     */
    public function transform($mixed)
    {
        $facade = new LogFacade();

        $facade->id = $mixed->getId();
        $facade->message = $this->translator->trans($mixed->getMessage());
        $facade->channel = $mixed->getChannel();
        $facade->level = $mixed->getLevel();
        $facade->dateTime = $mixed->getDateTime();
        $facade->levelName = $mixed->getLevelName();
        $facade->userIp = $mixed->getExtra()['user_ip'];
        $facade->userName = $mixed->getExtra()['user_name'];
        $facade->context = $mixed->getContext();

        return $facade;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'log';
    }
}
