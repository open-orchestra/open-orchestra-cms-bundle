FolderConfigurationButtonView = OrchestraView.extend(
  events:
    'click i.ajax-folder': 'clickEditFolder'

  initialize: (options) ->
    @options = @reduce(options, [
      'medias'
    ])
    @loadTemplates [
      "widgetFolderConfigurationButton"
    ]
    return

  render: ->
    @setElement @renderTemplate('widgetFolderConfigurationButton')
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
