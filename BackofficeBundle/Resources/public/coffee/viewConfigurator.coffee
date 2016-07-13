OrchestraViewConfigurator = ->
  configurations: {}
  baseConfigurations:
    'editEntity': FullPageFormView
    'addEntity': FullPageFormView
    'editEntityTab': TabElementFormView
    'showTab': TabView
    'addArea': OpenOrchestra.Page.Area.AreaView
    'showAreaToolbar': OpenOrchestra.Page.Area.AreaToolbarView
    'addBlock': OpenOrchestra.Page.Block.BlockView
    'addButtonAction': TableviewAction
    'addPageLayoutButton': OpenOrchestra.Page.PageLayoutButtonView
    'showTableCollection': TableviewCollectionView
    'showTableHeader': DataTableViewSearchHeader
    'addDataTable': DataTableView
    'showAdminForm': AdminFormView
    'showBlocksPanel': BlocksPanelView
    'showAddBlocksModal': OpenOrchestra.Page.Block.BlockFormAddView
    'showTemplate': OpenOrchestra.Page.Template.TemplateView
    'showNode': OpenOrchestra.Page.Node.NodeView
    'showLanguage': LanguageView
    'showDuplicate': DuplicateView
    'showPreviewLinks': PreviewLinkView
    'showStatus': StatusView
    'showVersion': VersionView
    'showVersionSelect': VersionSelectView
    'showOrchestraModal': OrchestraModalView
    'showFlashBag': FlashBagView
    'apiError': DisplayApiErrorView

  setConfiguration: (entityType, action, view) ->
    @configurations[entityType] = [] if typeof @configurations[entityType] == "undefined"
    @configurations[entityType][action] = view
    return

  getConfiguration: (entityType, action) ->
    entityTypeConfiguration = @configurations[entityType]
    if typeof entityTypeConfiguration != 'undefined'
      view = entityTypeConfiguration[action]
      if typeof view != 'undefined'
        return view
    return @baseConfigurations[action]

appConfigurationView = new OrchestraViewConfigurator()
