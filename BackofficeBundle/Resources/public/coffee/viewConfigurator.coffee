OrchestraViewConfigurator = ->
  configurations: {}
  baseConfigurations:
    'editEntity': FullPageFormView
    'addEntity': FullPageFormView
    'addArea': AreaView
    'addBlock': BlockView
    'addButtonAction': TableviewAction
    'addConfigurationButton': PageConfigurationButtonView
    'showTableCollection': TableviewCollectionView
    'showAdminForm': AdminFormView
    'showBlocksPanel': BlocksPanelView
    'showNode': NodeView
    'showTemplate': TemplateView
    'showLanguage': LanguageView
    'showDuplicate': DuplicateView
    'showPreviewLinks': PreviewLinkView
    'showStatus': StatusView
    'showVersion': VersionView
    'showVersionSelect': VersionSelectView
    'showOrchestraModal': OrchestraModalView
    'addFieldOptionDefaultValue': FieldOptionDefaultValueView

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

jQuery ->
  window.appConfigurationView = new OrchestraViewConfigurator()
