<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use OpenOrchestra\ApiBundle\Facade\DisplayedElementCollectionFacade;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\DependencyInjection\Container;
use OpenOrchestra\BaseApi\Transformer\AbstractTransformer;

/**
 * @deprecated will be removed in 0.2.4
 *
 * Class DisplayedElementCollectionTransformer
 */
class DisplayedElementCollectionTransformer extends AbstractTransformer
{

    protected $translator;

    /**
     * @param Container           $container
     * @param TranslatorInterface $translator
     */
    public function __construct(
        TranslatorInterface $translator
    ) {
        $this->translator = $translator;
    }

    /**
     * @param array $mixed
     *
     * @return DisplayedElementCollectionFacade|void
     */
    public function transform($mixed, $entityType = '')
    {

        $facade = new DisplayedElementCollectionFacade();

        $entityType = Container::camelize($entityType);
        $entityType = Container::underscore($entityType);

        foreach ($mixed as $displayedElement) {
            $facade->displayedElements[] = $this->translator->trans(
                'open_orchestra_backoffice.table.'.
                $entityType.
                '.'.
                $displayedElement
            );
        }

        return $facade;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'api_displayed_element';
    }
}
