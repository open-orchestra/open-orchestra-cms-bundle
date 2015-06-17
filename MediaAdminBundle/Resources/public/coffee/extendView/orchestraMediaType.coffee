extendView = extendView || {}
extendView['orchestraMediaType'] =
  events:
    'click .mediaModalOpen': 'launchMediaModal'
    'click .clear-media': 'clearMedia'
  
  currentLaunchModal: (modal, inputId, url, method) ->
    @openMediaModal(modal, inputId, url, method)
