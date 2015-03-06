SuperboxLoad = (folderId, mediaId) ->
  $.ajax
    url: appRouter.generateUrl('apiMediaEdit', mediaId: mediaId)
    method: 'GET'
    success: (response) ->
      if isLoginForm(response)
        redirectToLogin()
      else
        mediaModel = new GalleryModel
        mediaModel.set response
        view = new SuperboxView(
          media: mediaModel
          listUrl: appRouter.generateUrl('listFolder', folderId: folderId))
        appRouter.setCurrentMainView view
        return view
      return
  return
