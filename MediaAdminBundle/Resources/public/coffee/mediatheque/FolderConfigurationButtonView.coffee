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
    $('.modal-title').text $(event.target).html()
    new adminFormView(
      url: @options.medias.get('links')._self_folder
      deleteurl: @options.medias.get('links')._self_delete
    )

)
