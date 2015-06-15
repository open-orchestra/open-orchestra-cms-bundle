GalleryCollectionView = OrchestraView.extend(
  events:
    'click a.ajax-add': 'clickAdd'

  initialize: (options) ->
    @options = @reduceOption(options, [
      'medias'
      'title'
      'listUrl'
      'domContainer'
      'modal'
    ])
    @loadTemplates [
      "OpenOrchestraMediaAdminBundle:BackOffice:Underscore/galleryCollectionView",
      "OpenOrchestraMediaAdminBundle:BackOffice:Underscore/galleryView"
    ]
    return

  render: ->
    @setElement @renderTemplate('OpenOrchestraMediaAdminBundle:BackOffice:Underscore/galleryCollectionView'
      links: @options.medias.get('links')
    )
    @options.domContainer.html @$el
    $('.js-widget-title', @options.domContainer).text @options.title
    if !@options.modal
      @addConfigurationButton()
      @addDeleteButton()
    for mediaKey of @options.medias.get(@options.medias.get('collection_name'))
      @addElementToView (@options.medias.get(@options.medias.get('collection_name'))[mediaKey])
    $(".figure").width @options.domContainer.find("img").width()

  addElementToView: (mediaData) ->
    mediaModel = new GalleryModel
    mediaModel.set mediaData
    new GalleryView(@addOption(
      media: mediaModel
      domContainer: this.$el.find('.superbox')
    ))
    return

  clickAdd: (event) ->
    event.preventDefault()
    viewContext = @
    if $('#main .' + $(event.target).attr('class')).length
      displayLoader('div[role="container"]')
      Backbone.history.navigate('/add')
      $.ajax
        url: @options.medias.get('links')._self_add
        method: 'GET'
        success: (response) ->
          viewClass = appConfigurationView.getConfiguration('media', 'addEntity')
          new viewClass(viewContext.addOption(
            html: response
          ))

  addConfigurationButton: ->
    if @options.medias.get('links')._self_folder != undefined
      viewClass = appConfigurationView.getConfiguration('media', 'addFolderConfigurationButton')
      new viewClass(@options)

  addDeleteButton: ->
    if @options.medias.get('is_folder_deletable')
      if @options.medias.get('links')._self_delete != undefined
        viewClass = appConfigurationView.getConfiguration('media', 'addFolderDeleteButton')
        new viewClass(@options)
)
