GalleryLoad = (link, mediaView, target) ->
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
      viewClass = appConfigurationView.getConfiguration('media', mediaView)
      new viewClass(
        medias: medias
        title: title
        listUrl: listUrl
        domContainer: $(target)
        modal: target != '#content'
      )
