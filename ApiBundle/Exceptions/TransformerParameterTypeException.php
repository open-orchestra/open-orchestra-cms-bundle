<?php

namespace OpenOrchestra\ApiBundle\Exceptions;

use OpenOrchestra\BaseApi\Exceptions\ApiException;

@trigger_error('The '.__NAMESPACE__.'\TransformerParameterTypeException class is deprecated since version 1.2.0 and will be removed in 1.3.0, use OpenOrchestra\BaseApi\Exceptions\TransformerParameterTypeException', E_USER_DEPRECATED);

/**
 * Class TransformerParameterTypeException
 *
 * @deprecated will be removed in 1.3.0, use OpenOrchestra\BaseApi\Exceptions\TransformerParameterTypeException instead
 */
class TransformerParameterTypeException extends ApiException
{
}
