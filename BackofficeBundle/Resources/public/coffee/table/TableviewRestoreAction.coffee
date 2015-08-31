TableviewRestoreAction = OrchestraView.extend(
  events:
    'click a.ajax-restore': 'clickRestore'

  initialize: (options) ->
    @options = options
    _.bindAll this, "render"
    @loadTemplates [
      'OpenOrchestraBackofficeBundle:BackOffice:Underscore/tableviewRestoreAction'
    ]
    return

  render: ->
    @setElement $('<p />')
    @$el.append @renderTemplate('OpenOrchestraBackofficeBundle:BackOffice:Underscore/tableviewRestoreAction',
      links: @options.element.get('links')
    )
    @options.domContainer.html(@$el)

  clickRestore : (event) ->
    event.preventDefault()
    options = @options
    smartConfirm(
      'fa-undo',
      $('a',@$el).data('restore-confirm-title'),
      $('a',@$el).data('restore-confirm-txt'),
    callBackParams:
        url: @options.element.get('links')._self_restore
        row: $(event.target).closest('tr')
      yesCallback: (params) ->
        $.ajax
          url: params.url
          method: 'PUT'
          success : () ->
            params.row.hide()
            options.table.fnSettings().clearCache = true
            displayRoute = appRouter.generateUrl "listEntities",
              entityType: options.entityType
            displayMenu(displayRoute)
          error: (jqXHR) ->
            viewClass = appConfigurationView.getConfiguration('status', 'apiError')
            new viewClass(
              errors: jqXHR.responseJSON
            )
    )

)
