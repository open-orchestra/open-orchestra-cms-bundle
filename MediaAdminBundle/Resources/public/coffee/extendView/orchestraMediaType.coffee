extendView = extendView || {}
extendView['orchestraMediaType'] =
  events:
    'click .mediaModalOpen': 'mediaTypeModal'
    'click .clear-media': 'clearMedia'
  
  mediaTypeModal: (event) ->
    options = @launchMediaModal(event)
    @openMediaModal(options)
