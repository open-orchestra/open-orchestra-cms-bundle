FolderDeleteButtonView = OrchestraView.extend(
  events:
    'click i.ajax-folder-delete': 'clickDeleteFolder'

  initialize: (options) ->
    @options = @reduce(options, [
      'medias'
    ])
    @loadTemplates [
      "widgetFolderDeleteButton"
    ]
    return

  render: ->
    @setElement @renderTemplate('widgetFolderDeleteButton')
    addCustomJarvisWidget(@$el)
    return

  clickDeleteFolder: (event) ->
    event.preventDefault()
    smartConfirm(
      'fa-trash-o',
      $('.folder-delete').data('title'),
      $('.folder-delete').data('text'),
      callBackParams:
        folderDeleteButtonView: @
      yesCallback: (params) ->
        params.folderDeleteButtonView.deleteFolder()
    )

  deleteFolder: ->
    if @options.medias.get('parent_id') == undefined
      redirectUrl = appRouter.generateUrl('showHome')
    else
      redirectUrl = appRouter.generateUrl('listFolder', appRouter.addParametersToRoute(
        'folderId':  @options.medias.get('parent_id')
      ))
    $.ajax
      url:  @options.medias.get('links')._self_delete
      method: 'DELETE'
      success: ->
        Backbone.history.loadUrl(redirectUrl)
        displayMenu(redirectUrl)
)
