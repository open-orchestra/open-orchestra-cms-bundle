<?php

namespace OpenOrchestra\ApiBundle\OAuth2\Strategy;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Interface StrategyInterface
 */
interface StrategyInterface
{
    /**
     * @param Request $request
     *
     * @return boolean
     */
    public function supportRequestToken(Request $request);

    /**
     * @param Request $request [description]
     *
     * @return Response
     */
    public function requestToken(Request $request);

    /**
     * @return string
     */
    public function getName();
}
