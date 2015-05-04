FolderConfigurationButtonView = OrchestraView.extend(
  events:
    'click i.ajax-folder': 'clickEditFolder'

  initialize: (options) ->
    @options = @reduceOption(options, [
      'medias'
    ])
    @loadTemplates [
      "OpenOrchestraBackofficeBundle:BackOffice:Underscore/widgetFolderConfigurationButton"
    ]
    return

  render: ->
    @setElement @renderTemplate('OpenOrchestraBackofficeBundle:BackOffice:Underscore/widgetFolderConfigurationButton')
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
