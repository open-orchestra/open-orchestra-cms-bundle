FullPageFormView = OrchestraView.extend(
  events:
    'submit': 'addEventOnForm'

  initialize: (options) ->
    @initializer options
    @loadTemplates [
      'OpenOrchestraBackofficeBundle:BackOffice:Underscore/fullPageFormView',
      'OpenOrchestraBackofficeBundle:BackOffice:Underscore/elementTitle',
    ]
    return

  initializer: (options) ->
    @options = options
    @options.listUrl = appRouter.generateUrl('listEntities', entityType: options.entityType) if options.listUrl == undefined
    if @options.element != undefined
      @completeOptions @options.element,
        'multiLanguage': 'showEntityWithLanguageAndSourceLanguage'
        'multiVersion': 'showEntity'
        'duplicate': 'showEntity'
    @events = @events || {}

  render: ->
    @setElement @renderTemplate('OpenOrchestraBackofficeBundle:BackOffice:Underscore/fullPageFormView', @options)
    @options.domContainer.html @$el
    $('.js-widget-title', @options.domContainer).html @options.title
    $("[data-prototype]", @$el).each ->
      PO.formPrototypes.addPrototype $(this)
      return
    return

  addEventOnForm: (event)->
    event.preventDefault()
    viewContext = @
    viewClass = appConfigurationView.getConfiguration(@options.entityType, 'editEntity')
    $("form", @$el).ajaxSubmit
      context: button: $(".submit_form",event.target).parent()
      success: (response) ->
        new viewClass(viewContext.addOption(
          html: response
        ))
      error: (response) ->
        new viewClass(viewContext.addOption(
          html: response.responseText
        ))
    return
)
