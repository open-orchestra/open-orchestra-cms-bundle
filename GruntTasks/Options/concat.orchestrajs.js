module.exports = {
    src: [

        //--[ MAIN ]--//
        'web/built/openorchestrabackoffice/js/orchestraLib.js',
        'web/built/openorchestrabackoffice/js/viewConfigurator.js',
        'web/built/openorchestrabackoffice/js/setUpCallAjax.js',
        'web/built/openorchestrabackoffice/js/OrchestraView.js',
        'web/built/openorchestrabackoffice/js/OrchestraModalView.js',
        'web/built/openorchestrabackoffice/js/contentTypeFormAddFieldListener.js',
        'web/built/openorchestrabackoffice/js/addPrototype.js',
        'web/built/openorchestrabackoffice/js/modelBackbone.js',
        'web/built/openorchestrabackoffice/js/FullPageFormView.js',
        'web/built/openorchestrabackoffice/js/ContentTypeFormView.js',
        'web/built/openorchestrabackoffice/js/FullPagePanelView.js',
        'web/built/openorchestrabackoffice/js/page/nodeConstructor.js',
        'web/built/openorchestrabackoffice/js/page/BlocksPanelView.js',
        'web/built/openorchestrabackoffice/js/security.js',
        'web/built/openorchestrabackoffice/js/SmartConfirmView.js',
        'web/built/openorchestrabackoffice/js/AdminFormView.js',
        'web/built/openorchestrabackoffice/js/generateId.js',

        //--[ EXTEND VIEWS ]--//
        'web/built/openorchestrabackoffice/js/extendView/addArea.js',
        'web/built/openorchestrabackoffice/js/extendView/commonPage.js',
        'web/built/openorchestrabackoffice/js/extendView/generateId.js',
        'web/built/openorchestrabackoffice/js/extendView/showVideo.js',
        'web/built/openorchestrabackoffice/js/extendView/deleteTree.js',
        'web/built/openorchestrabackoffice/js/extendView/submitAdmin.js',
        'web/built/openorchestrabackoffice/js/extendView/contentTypeSelector.js',
        'web/built/openorchestrabackoffice/js/extendView/contentTypeChange.js',

        //--[ WIDGETS ]--//
        'web/built/openorchestrabackoffice/js/widget/widgetChannel.js',
        'web/built/openorchestrabackoffice/js/widget/duplicateChannel.js',
        'web/built/openorchestrabackoffice/js/widget/DuplicateView.js',
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

        //--[ PAGE ]--//
        'web/built/openorchestrabackoffice/js/page/makeSortable.js',
        'web/built/openorchestrabackoffice/js/page/AreaView.js',
        'web/built/openorchestrabackoffice/js/page/BlockView.js',
        'web/built/openorchestrabackoffice/js/page/NodeView.js',
        'web/built/openorchestrabackoffice/js/page/TemplateView.js',
        'web/built/openorchestrabackoffice/js/page/showNode.js',
        'web/built/openorchestrabackoffice/js/page/showTemplate.js',
        'web/built/openorchestrabackoffice/js/page/orderNode.js',
        'web/built/openorchestrabackoffice/js/page/PageConfigurationButtonView.js',
        'web/built/openorchestrabackoffice/js/page/viewportChannel.js',

        //--[ TABLEVIEW ]--//
        'web/built/openorchestrabackoffice/js/table/TableviewAction.js',
        'web/built/openorchestrabackoffice/js/table/TableviewCollectionView.js',
        'web/built/openorchestrabackoffice/js/table/tableviewLoader.js',

        //--[ MEDIA ]--//
        'web/built/openorchestramediaadmin/js/extendView/*.js',
        'web/built/openorchestramediaadmin/js/*.js',


        //--[ USER ]--//
        'web/built/openorchestrauseradmin/js/user/*.js',

        //--[ INDEXATION ]--//
        'web/bundles/openorchestraindexation/js/*.js',

        //--[ BACKBONE ROUTER ]--//
        'web/bundles/openorchestrabackoffice/js/backboneRouter.js'
    ],
    dest: 'web/built/orchestra.js'
};
