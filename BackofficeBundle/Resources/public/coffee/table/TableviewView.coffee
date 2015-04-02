TableviewView = OrchestraView.extend(
  tagName: 'tr'
  initialize: (options) ->
    @events = []
    @events['click a.ajax-delete'] = 'clickDelete'
    @events['click a.ajax-edit'] = 'clickEdit'
    @options = options
    _.bindAll this, "render"
    @loadTemplates [
      'tableviewView',
      'tableviewActions'
    ]
    return

  render: ->
    for displayedElement in @options.displayedElements
      @$el.append @renderTemplate('tableviewView'
        value: @options.element.get(displayedElement)
      )
    if Object.keys(@options.element.get('links')).length > 0
      @$el.append @renderTemplate('tableviewActions',
        deleted: @options.element.get('deleted')
        links: @options.element.get('links')
      )
    @options.target.append @$el
  clickDelete: (event) ->
    event.preventDefault()
    options = @options
    el = $(@el)
    smartConfirm(
      'fa-trash-o',
      'Delete this element',
      'The removal will be final',
      callBackParams:
        url: options.element.get('links')._self_delete
        row: el
      yesCallback: (params) ->
        $.ajax
          url: params.url
          method: 'DELETE'
        params.row.hide()
    )

  clickEdit: (event) ->
    event.preventDefault()
    options = @options
    parameters = 
      'entityId': @options.element.get('id')
      'language': @options.element.get('language')
      'version' : @options.element.get('version')
    route = 'showEntity'
    if @options.element.get('language')
      route = 'showEntityWithLanguage'
      if @options.element.get('version')
        route = 'showEntityWithLanguageAndVersion'
    redirectUrl = appRouter.generateUrl(route, appRouter.addParametersToRoute(parameters))
    Backbone.history.navigate(redirectUrl)
    $.ajax
      url: options.element.get('links')._self_form
      method: "GET"
      success: (response) ->
        options = $.extend(options, {html: response})
        view = new FullPageFormView(options)
        appRouter.setCurrentMainView view
)
