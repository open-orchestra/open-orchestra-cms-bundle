<?php

namespace PHPOrchestra\BackofficeBundle\DisplayBlock\Strategies;

use PHPOrchestra\DisplayBundle\DisplayBlock\DisplayBlockInterface;
use PHPOrchestra\DisplayBundle\DisplayBlock\Strategies\AbstractStrategy;
use PHPOrchestra\ModelBundle\Model\BlockInterface;
use PHPOrchestra\IndexationBundle\SearchStrategy\SearchManager;
use PHPOrchestra\ModelBundle\Repository\FieldIndexRepository;
use Solarium\Client;
use Solarium\QueryType\Select\Query\Query;
use Solarium\QueryType\Select\Result\Result;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class SearchResultStrategy
 */
class SearchResultStrategy extends AbstractStrategy
{
    protected $solariumClient;
    protected $searchManager;
    protected $translator;
    protected $container;
    protected $fieldIndexRepository;

    /**
     * @param Client               $solariumClient
     * @param SearchManager        $searchManager
     * @param TranslatorInterface  $translator
     * @param Container            $container
     * @param FieldIndexRepository $fieldIndexRepository
     */
    public function __construct(
        Client $solariumClient,
        SearchManager $searchManager,
        TranslatorInterface $translator,
        Container $container,
        FieldIndexRepository $fieldIndexRepository
    )
    {
        $this->solariumClient = $solariumClient;
        $this->searchManager = $searchManager;
        $this->translator = $translator;
        $this->container = $container;
        $this->fieldIndexRepository = $fieldIndexRepository;
    }

    /**
     * Check if the strategy support this block
     *
     * @param BlockInterface $block
     *
     * @return boolean
     */
    public function support(BlockInterface $block)
    {
        return DisplayBlockInterface::SEARCH_RESULT == $block->getComponent();
    }

    /**
     * Perform the show action for a block
     *
     * @param BlockInterface $block
     *
     * @return Response
     */
    public function show(BlockInterface $block)
    {
        $attributes = $block->getAttributes();

        return $this->render('PHPOrchestraBackofficeBundle:Block/SearchResult:show.html.twig',
            array(
                'nodeId' => $attributes['nodeId'],
                'nbdoc' => $attributes['nbdoc'],
                'fielddisplayed' => implode(', ', $attributes['fielddisplayed']),
                'nbfacet' => count($attributes['facets']),
                'nbfilter' => count($attributes['filter'])
            )
        );
    }

    /**
     * Get the name of the strategy
     *
     * @return string
     */
    public function getName()
    {
        return 'search_result';
    }
}
