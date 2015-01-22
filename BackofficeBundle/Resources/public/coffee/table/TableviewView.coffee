TableviewView = OrchestraView.extend(
  initialize: (options) ->
    @events = []
    @events['click a.ajax-delete-' + @cid] = 'clickDelete'
    @events['click a.ajax-edit-' + @cid] = 'clickEdit'
    @element = options.element
    @displayedElements = options.displayedElements
    @title = options.title
    @listUrl = options.listUrl
    _.bindAll this, "render"
    @loadTemplates [
      'tableviewView',
      'tableviewActions'
    ]
    return

  render: ->
    $(@el).append('<tr></tr>')
    row = $(@el).find('tr:last-of-type')
    for displayedElement in @displayedElements
      row.append @renderTemplate('tableviewView'
        value: @element.get(displayedElement)
      )
    row.append @renderTemplate('tableviewActions',
      deleted: @element.get('deleted')
      links: @element.get('links')
      cid: @cid
    )

  clickDelete: (event) ->
    event.preventDefault()
    smartConfirm(
      'fa-trash-o',
      'Delete this element',
      'The removal will be final',
      callBackParams:
        url: @element.get('links')._self_delete
        row: $(event.target).closest('tr')
      yesCallback: (params) ->
        $.ajax
          url: params.url
          method: 'DELETE'
        params.row.hide()
    )

  clickEdit: (event) ->
    event.preventDefault()
    if @element.get('language') && @element.get('version')
      redirectUrl = appRouter.generateUrl('showEntityWithLanguageAndVersion', appRouter.addParametersToRoute(
        'entityId': @element.get('id')
        'language': @element.get('language')
        'version' : @element.get('version')
      ))
    else if @element.get('language')
      redirectUrl = appRouter.generateUrl('showEntityWithLanguage', appRouter.addParametersToRoute(
        'entityId': @element.get('id')
        'language': @element.get('language')
      ))
    else
      redirectUrl = appRouter.generateUrl('showEntity', appRouter.addParametersToRoute(
        'entityId': @element.get('id')
      ))
    Backbone.history.navigate(redirectUrl)
    element = @element
    title = @title
    listUrl = @listUrl
    $.ajax
      url: element.get('links')._self_form
      method: "GET"
      success: (response) ->
        options =
          html: response
          title: title
          listUrl: listUrl
          element: element

        view = new FullPageFormView(options)
        appRouter.setCurrentMainView view
)
