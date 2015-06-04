adminFormView = OrchestraView.extend(
  initialize: (options) ->
    @options = @reduceOption(options, [
      'deleteUrl'
      'confirmText'
      'confirmTitle'
      'redirectUrl'
      'title'
      'url'
    ])
    @deleteButton = @options.deleteUrl && @options.confirmText && @options.confirmTitle
    @method = if options.method then options.method else 'GET'
    @events = @events || {}
    @loadTemplates [
        'OpenOrchestraBackofficeBundle:BackOffice:Underscore/deleteButton'
    ]
    return

  render: ->
    viewContext = this
    displayLoader('modal')
    $.ajax
      url: @options.url
      method: @method
      success: (response) ->
        body = $(response)
        if viewContext.deleteButton && $('form.form-disabled', body).length == 0
          footer = viewContext.renderTemplate('OpenOrchestraBackofficeBundle:BackOffice:Underscore/deleteButton', viewContext.options)
          footer.prepend($('.submit_form', body))
        new OrchestraModalView(
          body: body.html
          title: viewContext.options.title
          footer: footer.html
          domContainer: $('#OrchestraBOModal')
          extendView: ['submitAdmin']
        )
      error: ->
        new OrchestraModalView(
          body: 'Erreur durant le chargement'
          title: viewContext.options.title
          domContainer: $('#OrchestraBOModal')
        )
    return
)
