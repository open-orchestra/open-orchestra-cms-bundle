TableviewView = OrchestraView.extend(
  events:
    'click a.ajax-delete': 'clickDelete'
    'click a.ajax-edit' : 'clickEdit'

  initialize: (options) ->
    @options = options
    _.bindAll this, "render"
    @loadTemplates [
      'OpenOrchestraBackofficeBundle:BackOffice:Underscore/tableviewView',
      'OpenOrchestraBackofficeBundle:BackOffice:Underscore/tableviewActions'
    ]
    return

  render: ->
    @setElement $('<tr />')
    for displayedElement in @options.displayedElements
      @$el.append @renderTemplate('OpenOrchestraBackofficeBundle:BackOffice:Underscore/tableviewView'
        value: @options.element.get(displayedElement)
      )
    @$el.append @renderTemplate('OpenOrchestraBackofficeBundle:BackOffice:Underscore/tableviewActions',
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
        new FullPageFormView(viewContext.addOption(
          html: response
        ))
)
