extendView = extendView || {}
extendView['orchestraWysiwygType'] =

  launchWysiwygModal: (editorId) ->
    mediaModal = $(".select_media_modal")
    url = mediaModal.data('url')
    @openMediaModal(mediaModal , editorId, url, "GET")
    return

  openMediaModal: (modal, inputId, url, method) ->
    @abstractOpenMediaModal(modal, inputId, url, method, "showMediaWysiwyg")
    return
