<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use OpenOrchestra\ApiBundle\Facade\DatatableTranslationFacade;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseApi\Transformer\AbstractTransformer;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class DatatableTranslationTransformer
 */
class DatatableTranslationTransformer extends AbstractTransformer
{
    /**
     * @var TranslatorInterface
     */
    protected $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @param string $domain
     *
     * @return FacadeInterface
     */
    public function transform($domain)
    {
        $keyTranslation = "open_orchestra_datatable";
        $facade = new DatatableTranslationFacade();

        $facade->sProcessing = $this->translator->trans($keyTranslation.'.processing', array(), $domain);
        $facade->sSearch = $this->translator->trans($keyTranslation.'.search', array(), $domain);
        $facade->sLengthMenu = $this->translator->trans($keyTranslation.'.length_menu', array(), $domain);
        $facade->sInfo = $this->translator->trans($keyTranslation.'.info', array(), $domain);
        $facade->sInfoEmpty = $this->translator->trans($keyTranslation.'.info_empty', array(), $domain);
        $facade->sInfoFiltered = $this->translator->trans($keyTranslation.'.info_filtered', array(), $domain);
        $facade->sInfoPostFix= $this->translator->trans($keyTranslation.'.info_post_fix', array(), $domain);
        $facade->sLoadingRecords = $this->translator->trans($keyTranslation.'.loading_records', array(), $domain);
        $facade->sZeroRecords = $this->translator->trans($keyTranslation.'.zero_records', array(), $domain);
        $facade->buttons['colvis'] = $this->translator->trans($keyTranslation.'.buttons.colvis', array(), $domain);
        $facade->oPaginate["sFirst"] = $this->translator->trans($keyTranslation.'.paginate.first', array(), $domain);
        $facade->oPaginate["sLast"] = $this->translator->trans($keyTranslation.'.paginate.last', array(), $domain);
        $facade->oPaginate["sNext"] = $this->translator->trans($keyTranslation.'.paginate.next', array(), $domain);
        $facade->oPaginate["sPrevious"] = $this->translator->trans($keyTranslation.'.paginate.previous', array(), $domain);

        return $facade;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'datatable_translation';
    }
}
