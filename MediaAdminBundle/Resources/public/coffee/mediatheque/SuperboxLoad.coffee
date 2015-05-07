SuperboxLoad = (folderId, mediaId) ->
  $.ajax
    url: appRouter.generateUrl('apiMediaEdit', mediaId: mediaId)
    method: 'GET'
    success: (response) ->
      mediaModel = new GalleryModel
      mediaModel.set response
      new SuperboxView(
        media: mediaModel
        listUrl: appRouter.generateUrl('listFolder', folderId: folderId))
  return
