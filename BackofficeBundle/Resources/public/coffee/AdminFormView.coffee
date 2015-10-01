AdminFormView = OrchestraView.extend(
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
      'entityType'
    ])
    @deleteButton = @options.deleteUrl && @options.confirmText && @options.confirmTitle
    @method = if @options.method then @options.method else 'GET'
    @events = @events || {}
    @loadTemplates [
        'OpenOrchestraBackofficeBundle:BackOffice:Underscore/deleteButton'
    ]
    return

  render: ->
    viewContext = @
    @viewClass = appConfigurationView.getConfiguration(viewContext.options.entityType, 'showOrchestraModal')
    new @viewClass(
      html: "<h1><i class=\"fa fa-cog fa-spin\"></i> Loading...</h1>"
      title: "Please wait"
      domContainer: $('#OrchestraBOModal')
      entityType: viewContext.options.entityType
    )
    $.ajax
      url: @options.url
      method: @method
      success: (response) ->
        if viewContext.deleteButton && $('.form-disabled', response).length == 0
          originalButton = $('.submit_form', response)
          console.log $('.submit_form', response)
          button = originalButton.clone().attr('data-clone', originalButton.attr('id')).removeAttr('id')
          footer = $('<div>')
          .append(viewContext.renderTemplate('OpenOrchestraBackofficeBundle:BackOffice:Underscore/deleteButton', viewContext.options))
          .prepend(button)
          .html()
        extendView = viewContext.options.extendView || []
        if extendView.indexOf('submitAdmin') == -1
          extendView.push 'submitAdmin'
        new viewContext.viewClass(
          html: response
          title: viewContext.options.title
          footer: footer
          domContainer: $('#OrchestraBOModal')
          extendView: extendView
          entityType: viewContext.options.entityType
          formView: 'showOrchestraModal'
        )
    return
)
