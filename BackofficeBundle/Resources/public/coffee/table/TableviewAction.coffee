TableviewAction = OrchestraView.extend(
  events:
    'click a.ajax-delete': 'clickDelete'
    'click a.ajax-edit' : 'clickEdit'

  initialize: (options) ->
    @options = options
    _.bindAll this, "render"
    @loadTemplates [
      'OpenOrchestraBackofficeBundle:BackOffice:Underscore/table/tableviewActions'
    ]
    return

  render: ->
    @setElement $('<p />')
    @$el.append @renderTemplate('OpenOrchestraBackofficeBundle:BackOffice:Underscore/table/tableviewActions',
      deleted: @options.element.get('deleted')
      links: @options.element.get('links')
    )
    @options.domContainer.html(@$el)

  clickDelete: (event) ->
    event.preventDefault()
    options = @options
    smartConfirm(
      'fa-trash-o',
      $(event.currentTarget).data('title'),
      $(event.currentTarget).data('text'),
      callBackParams:
        url: @options.element.get('links')._self_delete
        row: $(event.target).closest('tr')
      yesCallback: (params) ->
        params.row.hide()
        OpenOrchestra.DataTable.Channel.trigger 'clearCache', options.tableId
        $.ajax
          url: params.url
          method: 'DELETE'
          complete: () ->
            OpenOrchestra.DataTable.Channel.trigger 'draw', options.tableId
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
    panels = false
    for key in Object.keys(links)
      if /^_self_panel_/.test(key)
        panels = true
        break
    $.ajax
      url: links._self_form
      method: "GET"
      success: (response) ->
        options = viewContext.addOption(html: response, domContainer: $('#content'))
        OpenOrchestra.DataTable.Channel.trigger 'clearCache', options.tableId
        if panels
          tabViewFormLoad(options)
        else
          viewClass = appConfigurationView.getConfiguration(viewContext.options.entityType, 'editEntity')
          new viewClass(options)
)
