<?php

namespace PHPOrchestra\CMSBundle\Controller\Block;

use Nelmio\SolariumBundle;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

/**
 * SearchResult Controller
 */
class SearchResultController extends Controller
{
    /**
     * Get list of words find by auto-completion with solr
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function autocompleteAction(Request $request)
    {
        // Take words in the search word field
        $terms = $request->query->get('term');

        // Create facet query
        $client = $this->get('solarium.client');
        $query = $client->createSelect();
        $query->setQuery('*:*');
        $facetSet = $query->getFacetSet();
        $facetSet->setLimit($request->get('limit', 5));
        $facet = $facetSet->createFacetField('autocomplete')->setField('suggest');
        $facet->setMinCount(1);
        $facet->setPrefix($terms);
        $resultset = $client->select($query);

        $result = array();
        foreach ($resultset->getFacetSet()->getFacets() as $facet) {
            $values = $facet->getValues();
            foreach ($values as $name => $value) {
                $result[] = $name;
            }
        }

        return new JsonResponse($result);
    }
}
