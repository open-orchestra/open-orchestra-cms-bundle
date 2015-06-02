TableviewAction = OrchestraView.extend(
  events:
    'click a.ajax-delete': 'clickDelete'
    'click a.ajax-edit' : 'clickEdit'

  initialize: (options) ->
    @options = options
    _.bindAll this, "render"
    @loadTemplates [
      'OpenOrchestraBackofficeBundle:BackOffice:Underscore/tableviewActions'
    ]
    return

  render: ->
    @setElement $('<p />')
    @$el.append @renderTemplate('OpenOrchestraBackofficeBundle:BackOffice:Underscore/tableviewActions',
      deleted: @options.element.get('deleted')
      links: @options.element.get('links')
    )
    @options.domContainer.html(@$el)

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
    links = options.element.get('links')
    panelKeys = []
    for key in Object.keys(links)
      if /^_self_panel_/.test(key)
        panelKeys.push(key)
    if panelKeys.length > 0
      appConfigurationView.setConfiguration(viewContext.options.entityType, 'edit', FullPagePanelView)
    $.ajax
      url: links._self_form
      method: "GET"
      success: (response) ->
        viewClass = appConfigurationView.getConfiguration(viewContext.options.entityType, 'edit')
        new viewClass(viewContext.addOption(html: response))
)
