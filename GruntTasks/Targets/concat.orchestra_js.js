module.exports = {
  src: [
    //--[ MAIN ]--//
    'web/built/openorchestrabackoffice/js/staticConfig.js',
    'web/built/openorchestrabackoffice/js/orchestraLib.js',
    'web/built/openorchestrabackoffice/js/tinyMCE/tinyMceConf.js',
    'web/built/openorchestrabackoffice/js/tinyMCE/plugins/BBCodeOrchestraPlugin.js',
    'web/built/openorchestrabackoffice/js/tinyMCE/plugins/LinkOrchestraPlugin.js',
    'web/built/openorchestrabackoffice/js/tinyMCE/bbcode2htmlConfigurator.js',
    'web/built/openorchestrabackoffice/js/tinyMCE/html2bbcodeConfigurator.js',
    'web/built/openorchestrabackoffice/js/tinyMCE/loadHtml2bbcodeInternalLink.js',
    'web/built/openorchestrabackoffice/js/setUpCallAjax.js',
    'web/built/openorchestrabackoffice/js/OrchestraView.js',
    'web/built/openorchestrabackoffice/js/OrchestraModalView.js',
    'web/built/openorchestrabackoffice/js/addPrototype.js',
    'web/built/openorchestrabackoffice/js/modelBackbone.js',
    'web/built/openorchestrabackoffice/js/FullPageFormView.js',
    'web/built/openorchestrabackoffice/js/ContentTypeFormView.js',
    'web/built/openorchestrabackoffice/js/FullPagePanelView.js',
    'web/built/openorchestrabackoffice/js/createNew.js',
    'web/built/openorchestrabackoffice/js/page-old/BlocksPanelView.js',
    'web/built/openorchestrabackoffice/js/security.js',
    'web/built/openorchestrabackoffice/js/SmartConfirmView.js',
    'web/built/openorchestrabackoffice/js/AdminFormView.js',
    'web/built/openorchestrabackoffice/js/FlashBagView.js',
    'web/built/openorchestrabackoffice/js/DisplayApiErrorView.js',
    'web/built/openorchestrabackoffice/js/generateId.js',
    'web/built/openorchestrabackoffice/js/forbiddenAccessRedirection.js',

    //--[ BUTTON RIBBON ]--//
    'web/built/openorchestrabackoffice/js/ribbonButton/RibbonFormButtonView.js',

    //--[ BACKBONE ROUTER ]--//
    'web/built/openorchestrabackoffice/js/backboneRouter.js',
    'web/built/openorchestrabackoffice/js/table/LoadTableConfigurationRoute.js',
    'web/built/openorchestrabackoffice/js/dashboard/LoadDashboardConfigurationRoute.js',
    'web/built/openorchestrabackoffice/js/underscoreTemplateLoader.js',
    'web/built/openorchestrabackoffice/js/user/LoadUserInfoRoute.js',

    //--[ EXTEND VIEWS ]--//
    'web/built/openorchestrabackoffice/js/extendView/addArea.js',
    'web/built/openorchestrabackoffice/js/extendView/commonPage.js',
    'web/built/openorchestrabackoffice/js/extendView/generateId.js',
    'web/built/openorchestrabackoffice/js/extendView/showVideo.js',
    'web/built/openorchestrabackoffice/js/extendView/submitAdmin.js',

    //--[ WIDGETS ]--//
    'web/built/openorchestrabackoffice/js/widget/widgetChannel.js',
    'web/built/openorchestrabackoffice/js/widget/newVersionChannel.js',
    'web/built/openorchestrabackoffice/js/widget/NewVersionView.js',
    'web/built/openorchestrabackoffice/js/widget/languageChannel.js',
    'web/built/openorchestrabackoffice/js/widget/LanguageView.js',
    'web/built/openorchestrabackoffice/js/widget/previewLinkChannel.js',
    'web/built/openorchestrabackoffice/js/widget/PreviewLinkView.js',
    'web/built/openorchestrabackoffice/js/widget/statusChannel.js',
    'web/built/openorchestrabackoffice/js/widget/StatusView.js',
    'web/built/openorchestrabackoffice/js/widget/versionChannel.js',
    'web/built/openorchestrabackoffice/js/widget/VersionSelectView.js',
    'web/built/openorchestrabackoffice/js/widget/VersionView.js',

    //--[ DASHBOARD ]--//
    'web/built/openorchestrabackoffice/js/dashboard/DashboardView.js',
    'web/built/openorchestrabackoffice/js/dashboard/widget/abstract/AbstractWidgetListView.js',
    'web/built/openorchestrabackoffice/js/dashboard/widget/abstract/AbstractWidgetNodeListView.js',
    'web/built/openorchestrabackoffice/js/dashboard/widget/abstract/AbstractWidgetContentListView.js',
    'web/built/openorchestrabackoffice/js/dashboard/widget/LastNodesView.js',
    'web/built/openorchestrabackoffice/js/dashboard/widget/DraftNodesView.js',
    'web/built/openorchestrabackoffice/js/dashboard/widget/LastContentsView.js',
    'web/built/openorchestrabackoffice/js/dashboard/widget/DraftContentsView.js',

    //--[ PAGE ]--//
    'web/built/openorchestrabackoffice/js/page/common/AbstractPageView.js',
    'web/built/openorchestrabackoffice/js/page/common/PageLayoutButtonView.js',

    //----- Block -----//
    'web/built/openorchestrabackoffice/js/page/block/Block.js',
    'web/built/openorchestrabackoffice/js/page/block/BlockCollection.js',
    'web/built/openorchestrabackoffice/js/page/block/BlockChannel.js',
    'web/built/openorchestrabackoffice/js/page/block/BlockFormAddView.js',
    'web/built/openorchestrabackoffice/js/page/block/BlockFormEditView.js',
    'web/built/openorchestrabackoffice/js/page/block/BlockView.js',

      //----- AREA  -----//
    'web/built/openorchestrabackoffice/js/page/area/Area.js',
    'web/built/openorchestrabackoffice/js/page/area/AreaCollection.js',
    'web/built/openorchestrabackoffice/js/page/area/AreaChannel.js',
    'web/built/openorchestrabackoffice/js/page/area/AreaAddRowExtendView.js',
    'web/built/openorchestrabackoffice/js/page/area/AreaAddBlockExtendView.js',
    'web/built/openorchestrabackoffice/js/page/area/AreaFormView.js',
    'web/built/openorchestrabackoffice/js/page/area/AreaFormRowView.js',
    'web/built/openorchestrabackoffice/js/page/area/AreaView.js',
    'web/built/openorchestrabackoffice/js/page/area/AreaToolbarView.js',

    //----- NODE -----//
    'web/built/openorchestrabackoffice/js/page/node/Node.js',
    'web/built/openorchestrabackoffice/js/page/node/NodeRouter.js',
    'web/built/openorchestrabackoffice/js/page/node/NodeView.js',
    'web/built/openorchestrabackoffice/js/page/node/NodeFormView.js',
    'web/built/openorchestrabackoffice/js/page/node/NodeLayoutButtonView.js',
    'web/built/openorchestrabackoffice/js/page/node/orderNode.js',

    //--[ TAB ]--//
    'web/built/openorchestrabackoffice/js/tab/TabView.js',
    'web/built/openorchestrabackoffice/js/tab/TabElementFormView.js',
    'web/built/openorchestrabackoffice/js/tab/tabViewFormLoader.js',

    //--[ DATATABLE ]--//
    'web/built/openorchestrabackoffice/js/dataTable/plugins/InputFullPagination.js',
    'web/built/openorchestrabackoffice/js/dataTable/header/searchField/*.js',
    'web/built/openorchestrabackoffice/js/dataTable/header/*.js',
    'web/built/openorchestrabackoffice/js/dataTable/*.js',

    //--[ TABLEVIEW ]--//
    'web/built/openorchestrabackoffice/js/table/TableOrchestraPagination.js',
    'web/built/openorchestrabackoffice/js/table/TableChannel.js',
    'web/built/openorchestrabackoffice/js/table/TableviewAction.js',
    'web/built/openorchestrabackoffice/js/table/TableviewTrashcanButtonAction.js',
    'web/built/openorchestrabackoffice/js/table/TableviewCollectionView.js',
    'web/built/openorchestrabackoffice/js/table/TableNoLinksviewCollectionView.js',
    'web/built/openorchestrabackoffice/js/table/tableviewLoader.js',

    //--[ USER ]--//
    'web/built/openorchestrabackoffice/js/user/UserFormView.js',
    'web/built/openorchestrauseradmin/js/user/*.js',

    //----- LOG -----//
    'web/built/openorchestrabackoffice/js/log/TableConfiguration.js',

    //--[ CONTENT TYPE ]--//
    'web/built/openorchestrabackoffice/js/contentType/TableviewCollectionView.js',

    //--[ WEBSITE ]--//
    'web/built/openorchestrabackoffice/js/webSite/WebSiteFormView.js',

    //--[ GROUP ]--//
    'web/built/openorchestrabackoffice/js/group/TableUserListActionView.js',
    'web/built/openorchestrabackoffice/js/group/GroupUserList.js',
    'web/built/openorchestrabackoffice/js/groupTree/*.js',

    //--[ INTERNAL LINK ]--//
    'web/built/openorchestrabackoffice/js/InternalLinkFormView.js',

    //--[ FORM BEHAVIOR ]--//
    'web/built/openorchestrabackoffice/js/formBehavior/formChannel.js',
    'web/built/openorchestrabackoffice/js/formBehavior/AbstractFormBehavior.js',
    'web/built/openorchestrabackoffice/js/formBehavior/FormLibraryBehavior.js',
    'web/built/openorchestrabackoffice/js/formBehavior/ColorPicker.js',
    'web/built/openorchestrabackoffice/js/formBehavior/DatePicker.js',
    'web/built/openorchestrabackoffice/js/formBehavior/HelpText.js',
    'web/built/openorchestrabackoffice/js/formBehavior/NodeChoice.js',
    'web/built/openorchestrabackoffice/js/formBehavior/RefreshForm.js',
    'web/built/openorchestrabackoffice/js/formBehavior/RichText.js',
    'web/built/openorchestrabackoffice/js/formBehavior/TagCondition.js',
    'web/built/openorchestrabackoffice/js/formBehavior/TagCreator.js',
    'web/built/openorchestrabackoffice/js/formBehavior/ValidateHidden.js',
    'web/built/openorchestrabackoffice/js/formBehavior/SelectGrouping.js',

    //--[ ERROR ]--//
    'web/built/openorchestrabackoffice/js/ErrorView.js',

    //--[ VIEWS CONFIGURATION ]--//
    'web/built/openorchestrabackoffice/js/viewConfigurator.js',

    //--[ ROUTING ]--//
    'web/js/fos_js_routes.js',

    //--[ TRANSLATION ]--//
    'web/js/translations/*/*.js',

    //--[ LIB ORCHESTRA ]--//
    'web/built/**/Lib/*.js',

    //--[ APPLICATION ]--//
    'web/built/oo_application.js'
  ],
  dest: 'web/built/orchestra.js'
};
