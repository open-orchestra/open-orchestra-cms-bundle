GalleryCollectionWysiwygView = GalleryCollectionView.extend(
  addElementToView: (mediaData) ->
    mediaModel = new GalleryModel
    mediaModel.set mediaData
    viewClass = appConfigurationView.getConfiguration('media', 'showGalleryWysiwyg')
    new viewClass(@addOption(
      media: mediaModel
      domContainer: this.$el.find('.superbox')
    ))
    return
)
