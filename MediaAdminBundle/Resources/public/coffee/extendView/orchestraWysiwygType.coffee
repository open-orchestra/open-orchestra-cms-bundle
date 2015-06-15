extendView = extendView || {}
extendView['orchestraWysiwygType'] =

  launchWysiwygModal: (editorId) ->
    mediaModal = $(".select_media_modal")
    url = mediaModal.data('url')
    @openMediaModal(mediaModal , editorId, url, "GET", "showMediaModalWysiwyg")
    return
