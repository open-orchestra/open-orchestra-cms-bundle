<?php

namespace PHPOrchestra\CMSBundle\DisplayBlock;

use PHPOrchestra\ModelBundle\Model\BlockInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Interface DisplayBlockInterface
 */
interface DisplayBlockInterface
{
    const CONTACT = 'Contact';
    const CARROUSEL = 'Carrousel';
    const FOOTER = 'Footer';
    const HEADER = 'Header';
    const MENU = 'Menu';
    const NEWS = 'News';
    const SAMPLE = 'Sample';
    const SEARCH = 'Search';
    const SEARCH_RESULT = 'SearchResult';
    const TINYMCEWYSIWYG = 'tinyMCEWysiwyg';

    /**
     * Check if the strategy support this block
     *
     * @param BlockInterface $block
     *
     * @return boolean
     */
    public function support(BlockInterface $block);

    /**
     * Perform the show action for a block
     *
     * @param BlockInterface $block
     *
     * @return Response
     */
    public function show(BlockInterface $block);

    /**
     * Perform the show action for a block on the backend
     *
     * @param BlockInterface $block
     *
     * @return Response
     */
    public function showBack(BlockInterface $block);

    /**
     * Get the name of the strategy
     *
     * @return string
     */
    public function getName();

    /**
     * Set the manager
     *
     * @param DisplayBlockManager $manager
     */
    public function setManager(DisplayBlockManager $manager);
}
