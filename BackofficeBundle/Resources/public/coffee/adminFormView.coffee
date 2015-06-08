adminFormView = OrchestraView.extend(
  initialize: (options) ->
    @options = @reduceOption(options, [
      'deleteUrl'
      'confirmText'
      'confirmTitle'
      'redirectUrl'
      'title'
      'url'
      'extendView'
      'method'
    ])
    @deleteButton = @options.deleteUrl && @options.confirmText && @options.confirmTitle
    @method = if @options.method then @options.method else 'GET'
    @events = @events || {}
    @loadTemplates [
        'OpenOrchestraBackofficeBundle:BackOffice:Underscore/deleteButton'
    ]
    return

  render: ->
    viewContext = this
    new OrchestraModalView(
      body: "<h1><i class=\"fa fa-cog fa-spin\"></i> Loading...</h1>"
      title: "Please wait"
      domContainer: $('#OrchestraBOModal')
    )
    $.ajax
      url: @options.url
      method: @method
      success: (response) ->
        body = $('<div>').append(response)
        if viewContext.deleteButton && $('form.form-disabled', body).length == 0
          footer = $('<div>')
          .append(viewContext.renderTemplate('OpenOrchestraBackofficeBundle:BackOffice:Underscore/deleteButton', viewContext.options))
          .prepend($('.submit_form', body)).html()
        body = body.html()
        extendView = viewContext.options.extendView || []
        if extendView.indexOf('submitAdmin') == -1
          extendView.push 'submitAdmin'
        new OrchestraModalView(
          body: body
          title: viewContext.options.title
          footer: footer
          domContainer: $('#OrchestraBOModal')
          extendView: extendView
        )
      error: ->
        new OrchestraModalView(
          body: 'Erreur durant le chargement'
          title: viewContext.options.title
          domContainer: $('#OrchestraBOModal')
        )
    return
)
