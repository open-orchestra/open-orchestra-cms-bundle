extendView = extendView || {}
extendView['orchestraMediaType'] =
  events:
    'click .mediaModalOpen': 'launchMediaModal'
    'click .clear-media': 'clearMedia'

  launchMediaModal: (event) ->
    event.preventDefault()
    target = $(event.currentTarget)
    url = target.data("url")
    @openMediaModal(modal, inputId, url, @method)
