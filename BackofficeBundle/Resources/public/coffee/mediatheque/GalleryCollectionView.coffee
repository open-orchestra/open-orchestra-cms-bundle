GalleryCollectionView = OrchestraView.extend(
  events:
    'click #none': 'clickAdd'

  initialize: (options) ->
    @medias = options.medias
    @title = options.title
    @listUrl = options.listUrl
    @target = options.el
    key = 'click a.ajax-add-' + @cid
    @events[key] = 'clickAdd'
    key = 'click i.ajax-folder-' + @cid
    @events[key] = 'clickEditFolder'
    key = 'click i.ajax-folder-delete-' + @cid
    @events[key] = 'clickDeleteFolder'
    _.bindAll this, "render"
    @loadTemplates [
      "galleryCollectionView",
      "galleryView"
    ]
    return

  render: ->
    $(@el).html @renderTemplate('galleryCollectionView',
      links: @medias.get('links')
      cid: @cid
    )
    $('.js-widget-title', @$el).text @title
    @addConfigurationButton()
    @addDeleteButton()
    for mediaKey of @medias.get(@medias.get('collection_name'))
      @addElementToView (@medias.get(@medias.get('collection_name'))[mediaKey])
    $(".figure").width $(this).find("img").width()
    $(".figure").mouseenter(->
      $(this).find(".caption").slideToggle(150)
      return
    ).mouseleave ->
      $(this).find(".caption").slideToggle(150)
      return

  addElementToView: (mediaData) ->
    mediaModel = new GalleryModel
    mediaModel.set mediaData
    view = new GalleryView(
      media: mediaModel
      title: @title
      listUrl: @listUrl
      el: this.$el.find('.superbox')
      target: @target
    )
    return

  clickAdd: (event) ->
    event.preventDefault()
    if $('#main .' + $(event.target).attr('class')).length
      displayLoader('div[role="container"]')
      Backbone.history.navigate('/add')
      title = @title
      listUrl = @listUrl
      $.ajax
        url: @medias.get('links')._self_add
        method: 'GET'
        success: (response) ->
          if isLoginForm(response)
            redirectToLogin()
          else
            view = new FullPageFormView(
              html: response
              title: title
              listUrl: listUrl
            )

  clickEditFolder: (event) ->
    event.preventDefault()
    $('.modal-title').text $(event.target).html()
    view = new adminFormView(
      url: @medias.get('links')._self_folder
      deleteurl: @medias.get('links')._self_delete
    )

  clickDeleteFolder: (event) ->
    event.preventDefault()
    smartConfirm(
      'fa-trash-o',
      'delete',
      'folder',
      callBackParams:
        galleryCollectionView: @
      yesCallback: (params) ->
        params.galleryCollectionView.deleteFolder()
    )

  deleteFolder: ->
    redirectUrl = appRouter.generateUrl('listFolder', appRouter.addParametersToRoute(
      'folderId': @medias.get('parent_id')
    ))
    $.ajax
      url: @medias.get('links')._self_delete
      method: 'DELETE'
      success: ->
        Backbone.history.loadUrl(redirectUrl)
        displayMenu(redirectUrl)


  addConfigurationButton: ->
    cid = @cid
    if @medias.get('links')._self_folder != undefined
      view = new FolderConfigurationButtonView(
        cid: cid
      )

  addDeleteButton: ->
    if @medias.get('is_folder_deletable')
      cid = @cid
      if @medias.get('links')._self_delete != undefined
        view = new FolderDeleteButtonView(
          cid: cid
        )
)
