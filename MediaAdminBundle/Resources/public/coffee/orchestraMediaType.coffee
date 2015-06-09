extendView = extendView || {}
extendView['orchestraMediaType'] =
  events:
    'click .mediaModalOpen': 'launchMediaModal'

  launchMediaModal: (event) ->
    event.preventDefault()
    target = $(event.currentTarget)
    url = target.data("url")
    @openMediaModal(modal, inputId, url, @method)
