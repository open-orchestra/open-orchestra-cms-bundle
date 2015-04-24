GalleryLoad = (link, target) ->
  if typeof target is "undefined"
    target = "#content"
  title = link.text()
  listUrl = Backbone.history.fragment
  $.ajax
    url: link.data('url')
    method: 'GET'
    success: (response) ->
      medias = new GalleryElement
      medias.set response
      view = new GalleryCollectionView(
        medias: medias
        title: title
        listUrl: listUrl
        domContainer: $(target)
        modal: target != '#content'
      )
      appRouter.setCurrentMainView(view)
