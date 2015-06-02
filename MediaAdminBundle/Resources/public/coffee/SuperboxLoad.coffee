SuperboxLoad = (folderId, mediaId) ->
  $.ajax
    url: appRouter.generateUrl('apiMediaEdit', mediaId: mediaId)
    method: 'GET'
    success: (response) ->
      mediaModel = new MediaModel
      mediaModel.set response
      viewClass = appConfigurationView.getConfiguration('media', 'showSuperbox')
      new viewClass(
        media: mediaModel
        listUrl: appRouter.generateUrl('listFolder', folderId: folderId))
  return
