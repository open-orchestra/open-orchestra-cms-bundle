extendView = extendView || {}
extendView['orchestraWysiwygType'] =
  events:
    'click .mce-btn[aria-label="mediamanager"] button': 'WysiwygTypeModal'

  WysiwygTypeModal: (event) ->
    options = @launchMediaModal(event)
    @openMediaModal($.extend(options, 
      galleryView : ['galleryWysiwygView']))
