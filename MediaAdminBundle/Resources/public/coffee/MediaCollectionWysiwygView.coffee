MediaCollectionWysiwygView = MediaCollectionView.extend(
  addElementToView: (mediaData) ->
    mediaModel = new MediaModel
    mediaModel.set mediaData
    viewClass = appConfigurationView.getConfiguration('media', 'showMediaCollectionWysiwyg')
    new viewClass(@addOption(
      media: mediaModel
      domContainer: this.$el.find('.superbox')
    ))
    return
)
