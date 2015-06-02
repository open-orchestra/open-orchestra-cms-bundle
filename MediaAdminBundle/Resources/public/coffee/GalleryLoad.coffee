GalleryLoad = (link, target) ->
  if typeof target is "undefined"
    target = "#content"
  title = link.text()
  listUrl = Backbone.history.fragment
  $.ajax
    url: link.data('url')
    method: 'GET'
    success: (response) ->
      medias = new MediaElement
      medias.set response
      viewClass = appConfigurationView.getConfiguration('media', 'showGalleryCollection')
      new viewClass(
        media  modal activation
        medias: medias
        title: title
        listUrl: listUrl
        domContainer: $(target)
        modal: target != '#content'
      )

