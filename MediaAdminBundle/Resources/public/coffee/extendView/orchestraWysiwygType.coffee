extendView = extendView || {}
extendView['orchestraWysiwygType'] =
  events:
    'click .mce-btn[aria-label="mediamanager"] button': 'launchMediaModal'

  currentLaunchModal: (modal, inputId, url, method) ->
    @openMediaModal(modal, inputId, url, method, "showGalleryWysiwyg")
