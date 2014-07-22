<?php

namespace PHPOrchestra\CMSBundle\DisplayBlock;
use PHPOrchestra\CMSBundle\Model\Block;
use Symfony\Component\HttpFoundation\Response;


/**
 * Interface DisplayBlockInterface
 */
interface DisplayBlockInterface
{
    const CONTACT = 'contact';
    const CARROUSEL = 'carrousel';
    const FOOTER = 'footer';
    const HEADER = 'header';
    const MENU = 'menu';
    const NEWS = 'news';
    const SAMPLE = 'sample';
    const SEARCH = 'search';
    const SEARCH_RESULT = 'SerchaResult';
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
