<?php

namespace OpenOrchestra\LogBundle\Transformer;

use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseApi\Transformer\AbstractTransformer;
use OpenOrchestra\LogBundle\Facade\LogFacade;
use OpenOrchestra\LogBundle\Model\LogInterface;
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

        $extra = array(
            'user_ip' => '0.0.0.0',
            'user_name' => 'Unknown',
            'site_name' => ''
        );
        $extra = array_merge($extra, $mixed->getExtra());

        $facade->id = $mixed->getId();
        $facade->message = $mixed->getMessage();
        $context = $mixed->getContext();
        if (!empty($context)) {
            $facade->message = $this->translator->trans($mixed->getMessage(), $mixed->getContext());
        }
        $facade->channel = $mixed->getChannel();
        $facade->level = $mixed->getLevel();
        $facade->dateTime = $mixed->getDateTime();
        $facade->levelName = $mixed->getLevelName();
        $facade->userIp = $extra['user_ip'];
        $facade->userName = $extra['user_name'];
        $facade->siteName = $extra['site_name'];

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
