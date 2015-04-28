TableviewView = OrchestraView.extend(
  events:
    'click a.ajax-delete': 'clickDelete'
    'click a.ajax-edit' : 'clickEdit'

  initialize: (options) ->
    @options = options
    _.bindAll this, "render"
    @loadTemplates [
      'tableviewView',
      'tableviewActions'
    ]
    return

  render: ->
    @setElement $('<tr />')
    for displayedElement in @options.displayedElements
      @$el.append @renderTemplate('tableviewView'
        value: @options.element.get(displayedElement)
      )
    @$el.append @renderTemplate('tableviewActions',
      deleted: @options.element.get('deleted')
      links: @options.element.get('links')
    )
    @options.domContainer.append(@$el)

  clickDelete: (event) ->
    event.preventDefault()
    smartConfirm(
      'fa-trash-o',
      'Delete this element',
      'The removal will be final',
      callBackParams:
        url: @options.element.get('links')._self_delete
        row: $(event.target).closest('tr')
      yesCallback: (params) ->
        $.ajax
          url: params.url
          method: 'DELETE'
        params.row.hide()
    )

  clickEdit: (event) ->
    event.preventDefault()
    parameter = 
      'entityId': @options.element.get('id')
      'language': @options.element.get('language')
      'version' : @options.element.get('version')
    redirectUrl = 'showEntity'
    if @options.element.get('language')
      redirectUrl = 'showEntityWithLanguage'
      if @options.element.get('version')
        redirectUrl = 'showEntityWithLanguageAndVersion'
    redirectUrl = appRouter.generateUrl(redirectUrl, appRouter.addParametersToRoute(parameter))
    Backbone.history.navigate(redirectUrl)
    options = @options
    viewContext = @
    $.ajax
      url: options.element.get('links')._self_form
      method: "GET"
      success: (response) ->
        view = new FullPageFormView(viewContext.addOption(
          html: response
        ))
        appRouter.setCurrentMainView view
)
