<?php

namespace PHPOrchestra\CMSBundle\DisplayBlock;
use PHPOrchestra\CMSBundle\Model\Block;
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
     * @param Block $block
     *
     * @return boolean
     */
    public function support(Block $block);

    /**
     * Perform the show action for a block
     *
     * @param Block $block
     *
     * @return Response
     */
    public function show(Block $block);

    /**
     * Perform the show action for a block on the backend
     *
     * @param Block $block
     *
     * @return Response
     */
    public function showBack(Block $block);

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
