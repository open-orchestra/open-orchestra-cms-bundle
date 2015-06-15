currentModal = null

MediaModalWysiwygView = MediaModalView.extend(
    showFolder: (event) ->
      displayLoader $(".modal-body-content", @$el)
      GalleryLoad $(event.target), "showGalleryCollectionWysiwyg", $(".modal-body-content", @$el)
  )
