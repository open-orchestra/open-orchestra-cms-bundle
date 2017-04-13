<?php

namespace OpenOrchestra\Backoffice\Event;

use OpenOrchestra\ModelInterface\Model\SiteInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class SiteCommandEvent
 */
class SiteCommandEvent extends Event
{
    protected $site;
    protected $io;

    /**
     * @param SiteInterface $site
     * @param SymfonyStyle  $io
     */
    public function __construct(SiteInterface $site, SymfonyStyle $io)
    {
        $this->site = $site;
        $this->io = $io;
    }

    /**
     * @return SymfonyStyle
     */
    public function getIo()
    {
        return $this->io;
    }

    /**
     * @return SiteInterface
     */
    public function getSite()
    {
        return $this->site;
    }
}
