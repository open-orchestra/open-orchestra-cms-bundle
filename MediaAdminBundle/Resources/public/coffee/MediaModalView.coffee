formChannel = new (Backbone.Wreqr.EventAggregator)

currentModal = null

MediaModalView = OrchestraView.extend(
  events:
    'click .mediaModalClose': 'closeModal'
    'click .media-modal-menu-folder' : 'showFolder'
    'click .ajax-add': 'openForm'
    'click .media-modal-menu-new-folder' : 'openForm'

  initialize: (options) ->
    @options = @reduceOption(options, [
      'body'
      'input'
      'domContainer'
      'url'
      'galleryView'
    ])
    @loadTemplates [
      "OpenOrchestraMediaAdminBundle:BackOffice:Underscore/mediaModalView"
    ]
    return

  render: (options) ->
    @setElement @renderTemplate('OpenOrchestraMediaAdminBundle:BackOffice:Underscore/mediaModalView', @options)
    if currentModal != null
      $('.modal-dialog', currentModal).replaceWith $('.modal-dialog', @$el)
    else
      @options.domContainer.html @$el
      currentModal = @$el.detach().appendTo('body')
      @$el.modal "show"

  closeModal: ->
    @$el.modal "hide"
    @$el.remove()
    currentModal = null

  showFolder: (event) ->
    viewContext = @
    displayLoader $(".modal-body-content", @$el)
    GalleryLoad $(event.target), @options.galleryView, $(".modal-body-content", @$el)

  reloadFolder: ->
    displayLoader $('.modal-body-menu', @$el)
    viewContext = @
    $.ajax
      url: @options.url
      method: 'GET'
      success: (response) ->
        $('.modal-body-menu', currentModal).html response
    return

  openForm: (event) ->
    event.preventDefault()
    displayLoader $(".modal-body-content", @$el)
    folderName = $(".js-widget-title", @$el).text()
    domContainer = $(".modal-body-content", @$el)
    @listenToOnce(formChannel, 'formSubmit', @reloadFolder) if $(event.target).hasClass('media-modal-menu-new-folder')
    $.ajax
      url: $(event.target).attr('data-url')
      method: 'GET'
      success: (response) ->
        viewClass = appConfigurationView.getConfiguration('media', 'showMediaForm')
        new viewClass(
            html: response
            domContainer: domContainer
            title: $.trim(folderName)
          )
)
