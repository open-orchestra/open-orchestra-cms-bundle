jQuery ->
  baseMediaConfiguration =
    'addFolderConfigurationButton': FolderConfigurationButtonView
    'addFolderDeleteButton': FolderDeleteButtonView
    'showGalleryCollection': GalleryCollectionView
    'showSuperbox': SuperboxView
    'showMediaForm': MediaFormView
    'showMediaModal': MediaModalView
    'showGallery': GalleryView
    'showWysiwygSelect': WysiwygSelectView

  $.extend true, window.appConfigurationView.baseConfigurations,baseMediaConfiguration
