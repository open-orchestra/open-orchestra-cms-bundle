mediaFormView = OrchestraView.extend(
  events:
    'submit': 'addEventOnForm'

  initialize: (options) ->
    @options = @reduceOption(options, [
      'html'
      'title'
      'domContainer'
    ])
    @loadTemplates [
      'OpenOrchestraBackofficeBundle:BackOffice:Underscore/fullPageFormView'
    ]
    return

  render: ->
    @setElement @renderTemplate('OpenOrchestraBackofficeBundle:BackOffice:Underscore/fullPageFormView',
      html: @options.html
    )
    @options.domContainer.html @$el
    $('.js-widget-title', @options.domContainer).html @options.title
    $('.back-to-list', @options.domContainer).remove()
    $("[data-prototype]", @options.domContainer).each ->
      PO.formPrototypes.addPrototype $(this)
      return
    return

  addEventOnForm: (event) ->
    event.preventDefault()
    viewContext = @
    $('form', @options.domContainer).ajaxSubmit
      context:
        button: $(".submit_form",event.currentTarget).parent()
      success: (response) ->
        new mediaFormView(viewContext.addOption(
          html: response
        ))
    return
)
