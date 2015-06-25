FullPageFormView = OrchestraView.extend(

  extendView : [ 'submitAdmin' ]

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
    @options.formView = 'editEntity'
    @events = @events || {}

  render: ->
    @setElement @renderTemplate('OpenOrchestraBackofficeBundle:BackOffice:Underscore/fullPageFormView', @options)
    @options.domContainer.html @$el
    $('.js-widget-title', @options.domContainer).html @options.title
    $("[data-prototype]", @$el).each ->
      PO.formPrototypes.addPrototype $(this)
      return
    return
)
