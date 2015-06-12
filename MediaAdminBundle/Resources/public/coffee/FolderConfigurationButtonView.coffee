FolderConfigurationButtonView = OrchestraView.extend(
  events:
    'click i.ajax-folder': 'clickEditFolder'

  initialize: (options) ->
    @options = @reduceOption(options, [
      'medias'
    ])
    @loadTemplates [
      "OpenOrchestraMediaAdminBundle:BackOffice:Underscore/widgetFolderConfigurationButton"
    ]
    return

  render: ->
    @setElement @renderTemplate('OpenOrchestraMediaAdminBundle:BackOffice:Underscore/widgetFolderConfigurationButton')
    addCustomJarvisWidget(@$el)
    return

  clickEditFolder: (event) ->
    event.preventDefault()
    viewClass = appConfigurationView.getConfiguration('media', 'showAdminForm')
    new viewClass(
      url: @options.medias.get('links')._self_folder
      deleteurl: @options.medias.get('links')._self_delete
      title: $(event.target).html()
      entityType: 'media'
    )

)
