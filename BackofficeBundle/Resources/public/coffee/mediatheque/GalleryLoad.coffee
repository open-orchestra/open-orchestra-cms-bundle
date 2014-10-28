GalleryLoad = (link, target) ->
  if typeof target is "undefined"
    target = "#content"
  title = link.text()
  listUrl = Backbone.history.fragment
  $.ajax
    url: link.data('url')
    method: 'GET'
    success: (response) ->
      if isLoginForm(response)
        redirectToLogin()
      else
        medias = new GalleryElement
        medias.set response
        view = new GalleryCollectionView(
          medias: medias
          title: title
          listUrl: listUrl
          el: target
        )
        appRouter.setCurrentMainView(view)
