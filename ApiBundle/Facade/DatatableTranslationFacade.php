<?php

namespace OpenOrchestra\ApiBundle\Facade;

use JMS\Serializer\Annotation as Serializer;

/**
 * Class DatatableTranslationFacade
 */
class DatatableTranslationFacade
{
    /**
     * @Serializer\Type("string")
     * @Serializer\SerializedName("sProcessing")
     */
    public $sProcessing;

    /**
     * @Serializer\Type("string")
     * @Serializer\SerializedName("sSearch")
     */
    public $sSearch;

    /**
     * @Serializer\Type("string")
     * @Serializer\SerializedName("sLengthMenu")
     */
    public $sLengthMenu;

    /**
     * @Serializer\Type("string")
     * @Serializer\SerializedName("sInfo")
     */
    public $sInfo;

    /**
     * @Serializer\Type("string")
     * @Serializer\SerializedName("sInfoEmpty")
     */
    public $sInfoEmpty;

    /**
     * @Serializer\Type("string")
     * @Serializer\SerializedName("sInfoFiltered")
     */
    public $sInfoFiltered;

    /**
     * @Serializer\Type("string")
     * @Serializer\SerializedName("sInfoPostFix")
     */
    public $sInfoPostFix;

    /**
     * @Serializer\Type("string")
     * @Serializer\SerializedName("sLoadingRecords")
     */
    public $sLoadingRecords;

    /**
     * @Serializer\Type("string")
     * @Serializer\SerializedName("sZeroRecords")
     */
    public $sZeroRecords;

    /**
     * @Serializer\Type("string")
     * @Serializer\SerializedName("sEmptyTable")
     */
    public $sEmptyTable;

    /**
     * @Serializer\Type("array<string,string>")
     * @Serializer\SerializedName("oPaginate")
     */
    public $oPaginate = array();

    /**
     * @Serializer\Type("array<string,string>")
     */
    public $buttons = array();
}
