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
    options = @options
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
        options.table.fnSettings().clearCache = true
    )

  clickEdit: (event) ->
    event.preventDefault()
    parameter =
      'entityType': @options.entityType
      'entityId': @options.element.get('id')
      'language': @options.element.get('language')
      'version' : @options.element.get('version')
    redirectUrl = 'showEntity'
    redirectUrl = appRouter.generateUrl(redirectUrl, parameter)
    Backbone.history.navigate(redirectUrl)
    options = @options
    viewContext = @
    links = options.element.get('links')
    for key in Object.keys(links)
      if /^_self_panel_/.test(key)
        appConfigurationView.setConfiguration(viewContext.options.entityType, 'editEntity', FullPagePanelView)
        break
    $.ajax
      url: links._self_form
      method: "GET"
      success: (response) ->
        viewClass = appConfigurationView.getConfiguration(viewContext.options.entityType, 'editEntity')
        new viewClass(viewContext.addOption(html: response, domContainer: $('#content')))
        viewContext.options.table.fnSettings().clearCache = true
)
