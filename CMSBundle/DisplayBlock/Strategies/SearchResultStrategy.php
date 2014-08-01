<?php

namespace PHPOrchestra\CMSBundle\DisplayBlock\Strategies;

use PHPOrchestra\CMSBundle\DisplayBlock\DisplayBlockInterface;
use PHPOrchestra\ModelBundle\Model\BlockInterface;
use PHPOrchestra\IndexationBundle\SearchStrategy\SearchManager;
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

    /**
     * @param Client              $solariumClient
     * @param SearchManager       $searchManager
     * @param TranslatorInterface $translator
     * @param Container           $container
     */
    public function __construct(
        Client $solariumClient,
        SearchManager $searchManager,
        TranslatorInterface $translator,
        Container $container
    )
    {
        $this->solariumClient = $solariumClient;
        $this->searchManager = $searchManager;
        $this->translator = $translator;
        $this->container = $container;
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
        $nodeId = $attributes['nodeId'];
        $nbdoc = $attributes['nbdoc'];
        $fielddisplayed = $attributes['fielddisplayed'];
        $nbspellcheck = (array_key_exists('nbspellcheck', $attributes)?$attributes['nbspellcheck']:5);
        $limitField = (array_key_exists('limitField', $attributes)?$attributes['limitField']:50);
        $facets = (array_key_exists('facets', $attributes)?$attributes['facets']:array());
        $filter = (array_key_exists('filter', $attributes)?$attributes['filter']:array());
        $optionsearch = (array_key_exists('optionsearch', $attributes)?$attributes['optionsearch']:array());
        $optionsdismax = (array_key_exists('optiondismax', $attributes)?$attributes['optinodismax']:array());
        $page = (array_key_exists('page', $attributes)?$attributes['page']:1);
        if ($this->getRequest()) {
            $_page_parameters['query'] = $this->getRequest()->query->all();
        } else {
            $_page_parameters = array();
        }

        if (isset($_page_parameters['query']) && is_array($_page_parameters['query'])) {
            $paramQuery = $_page_parameters['query'];
            if (isset($paramQuery['autocomplete_search']) || isset($paramQuery['terms'])) {

                if (isset($paramQuery['autocomplete_search'])) {
                    $data = $paramQuery['autocomplete_search']['terms'];
                } else {
                    $data = $paramQuery['terms'];
                }

                if (isset($paramQuery['page'])) {
                    $page = $paramQuery['page'];
                }

                $optionsearch = array_merge(
                    array('start' => ($page * $nbdoc) - $nbdoc, 'rows' => $page * $nbdoc),
                    $optionsearch
                );

                // Result of search
                $resultSet = $this->callResearch(
                    $data,
                    $nbspellcheck,
                    $optionsearch,
                    $facets,
                    $filter,
                    $optionsdismax
                );

                // Call template
                return $this->callTemplate(
                    $data,
                    $resultSet,
                    $nodeId,
                    $page,
                    $nbdoc,
                    $fielddisplayed,
                    $limitField,
                    $facets
                );

            } else {
                // Filter
                if (isset($paramQuery['data']) && isset($paramQuery['filter']) && isset($paramQuery['facetname'])) {

                    if (isset($paramQuery['page'])) {
                        $page = $paramQuery['page'];
                    }

                    // Result of filter query
                    $resultSet = $this->callFilter($paramQuery['data'], $paramQuery['filter'], $paramQuery['facetname']);

                    // Call template
                    return $this->callTemplate(
                        $paramQuery['data'],
                        $resultSet,
                        $nodeId,
                        $page,
                        $nbdoc,
                        $fielddisplayed,
                        $limitField,
                        $facets
                    );
                }
            }
        }

        return new Response($this->translator->trans('php_orchestra_cms.search_result.no_result_found'));
    }

    /**
     * Perform the show action for a block on the backend
     *
     * @param BlockInterface $block
     *
     * @return Response
     */
    public function showBack(BlockInterface $block)
    {
        return $this->show($block);
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
    /**
     * Search in solr
     *
     * @param string $data         searching word
     * @param int    $nbspellcheck number of spellcheck result
     * @param array  $optionSearch array of option to the search
     * @param array  $facets       array of option to the facets
     * @param array  $filters      array of filters
     * @param array  $dismax       array of dismax options
     *
     * @return Result
     */
    protected function callResearch($data, $nbspellcheck, $optionSearch, $facets, $filters, $dismax)
    {
        // Research
        $query = $this->searchManager->search($data, null, $optionSearch);

        // Spell check setting
        $query = $this->searchManager->spellCheck($query, $data, $nbspellcheck);

        // Faceting
        if (isset($facets) && !empty($facets)) {
            $this->callFacet($query, $this->searchManager, $facets);
        }

        // Filtering
        if (isset($filters) && !empty($filters)) {
            if (!isset($filters['name'])) {
                // array of filter
                foreach ($filters as $filter) {
                    $this->searchManager->filter($query, $filter['name'], $filter['field']);
                }
            } else {
                $this->searchManager->filter($query, $filters['name'], $filters['field']);
            }
        }

        /*/ Dismax
        if (isset($dismax) && !empty($dismax)) {
            if (isset($dismax['mm'])) {
                $this->searchManager->disMax($query, $dismax['fields'], $dismax['boost'], $dismax['mm']);
            } else {
                $this->searchManager->disMax($query, $dismax['fields'], $dismax['boost']);
            }
        }*/

        return $this->result($query, $this->searchManager);
    }

    /**
     * Create a filter query
     *
     * @param string $data      search word
     * @param string $filter    query filter
     * @param string $facetName facet name
     *
     * @return Result
     */
    protected function callFilter($data, $filter, $facetName)
    {
        $query  = $this->solariumClient->createSelect();

        $query->setQuery($data);
        $query->createFilterQuery($facetName)->setQuery($filter);

        return $this->solariumClient->select($query);
    }

    /**
     * Return result of the query
     *
     * @param Query         $query
     * @param SearchManager $search
     *
     * @return mixed|NULL|array|Result
     */
    protected function result($query, $search)
    {
        // Result
        $resultset = $this->searchManager->select($query);

        if ($resultset->getNumFound() < 1) {
            $result = array();
            $spellcheck = $resultset->getSpellcheck();

            if (isset($spellcheck)) {
                $suggestions = $resultset->getSpellcheck()->getSuggestions();

                if (isset($suggestions)) {
                    foreach ($suggestions as $suggest) {
                        $search->search($suggest->getword(), $query);

                        $result[] = $this->container
                            ->get('php_orchestra_indexation.search_manager')
                            ->select($query);
                    }
                }
            }

            return $result;
        } else {
            return $resultset;
        }
    }

    /**
     * Call facet services
     *
     * @param Query         $query  query
     * @param SearchManager $search search services
     * @param array         $facets array of facets
     */
    protected function callFacet($query, $search, $facets)
    {
        $facetSet = $query->getFacetSet();

        if (isset($facets['facetField'])) {
            if (isset($facets['facetField']['name']) && isset($facets['facetField']['field'])) {
                if (isset($facets['facetField']['options'])) {
                    $search->facetField(
                        $facetSet,
                        $facets['facetField']['name'],
                        $facets['facetField']['field'],
                        $facets['facetField']['options']
                    );
                } else {
                    $search->facetField(
                        $facetSet,
                        $facets['facetField']['name'],
                        $facets['facetField']['field']
                    );
                }
            }
        }
        if (isset($facets['facetQuery'])) {
            if (isset($facets['facetQuery']['field']) && isset($facets['facetQuery']['query'])) {
                $search->facetQuery(
                    $facetSet,
                    $facets['facetQuery']['field'],
                    $facets['facetQuery']['query']
                );
            }
        }
        if (isset($facets['multiQuery'])) {
            if (isset($facets['multiQuery']['field']) && isset($facets['multiQuery']['queries'])) {
                $search->facetmultiQUery(
                    $facetSet,
                    $facets['multiQuery']['field'],
                    $facets['multiQuery']['queries']
                );
            }
        }
        if (isset($facets['facetRange'])) {
            if (isset($facets['facetRange']['name']) && isset($facets['field'])
                && isset($facets['facetRange']['start']) && isset($facets['facetRange']['gap'])
                && isset($facets['facetRange']['end'])) {
                $search->facetRange(
                    $facetSet,
                    $facets['facetRange']['name'],
                    $facets['facetRange']['field'],
                    $facets['facetRange']['start'],
                    $facets['facetRange']['gap'],
                    $facets['facetRange']['end']
                );
            }
        }
    }

    /**
     * Call search template
     *
     * @param string $data       search word
     * @param Result $resultSet  object solarium result
     * @param string $nodeId     identifiant of node
     * @param int    $page       number of page
     * @param int    $nbdoc      number of documents per page selected by the user
     * @param array  $fields     array of field displayed
     * @param int    $limitField number of letters per field
     * @param array  $facets     array if they have facets
     *
     * @return Response
     */
    protected function callTemplate($data, $resultSet, $nodeId, $page, $nbdoc, $fields, $limitField, $facets = array())
    {
        $firstField = array_shift($fields);
        if (isset($facets)) {
            return $this->render(
                "PHPOrchestraCMSBundle:Block/SearchResult:show.html.twig",
                array(
                    'data' => $data,
                    'resultset' => $resultSet,
                    'nodeId' => $nodeId,
                    'page' => $page,
                    'nbdocs' => $nbdoc,
                    'fieldsdisplayed' => $fields,
                    'facetsArray' => $facets,
                    'firstField' => $firstField,
                    'limitField' => $limitField
                )
            );
        } else {
            return $this->render(
                "PHPOrchestraCMSBundle:Block/SearchResult:show.html.twig",
                array(
                    'data' => $data,
                    'resultset' => $resultSet,
                    'nodeId' => $nodeId,
                    'page' => $page,
                    'nbdocs' => $nbdoc,
                    'fieldsdisplayed' => $fields,
                    'firstField' => $firstField,
                    'limitField' => $limitField
                )
            );
        }
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->container->get('request');
    }
}
