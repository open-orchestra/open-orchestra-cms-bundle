extendView = extendView || {}
extendView['galleryWysiwygView'] =
  mediaSelect: (event) ->
    event.preventDefault()
    viewContext = @
    $.ajax
      url: @options.media.get('links')._self_crop
      method: "GET"
      success: (response) ->
        viewClass = appConfigurationView.getConfiguration('media', 'showWysiwygSelect')
        new viewClass(
          domContainer: viewContext.$el.closest(".modal-body-content")
          html: response
          thumbnails: viewContext.options.thumbnails
        )
