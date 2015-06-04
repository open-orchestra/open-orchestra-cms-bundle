adminFormView = OrchestraView.extend(
  el: '#OrchestraBOModal'

  initialize: (options) ->
    @options = @reduceOption(options, [
      'deleteUrl'
      'confirmText'
      'confirmTitle'
      'redirectUrl'
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
    displayLoader('.modal-body')
    $("#OrchestraBOModal").modal "show"
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
          title: $('#dynamic-modal-title').html()
          footer: footer.html
          domContainer: $('#OrchestraBOModal')
          extendView: ['submitAdmin']
      error: ->
        $('.modal-body', viewContext.el).html 'Erreur durant le chargement'
    return
)
