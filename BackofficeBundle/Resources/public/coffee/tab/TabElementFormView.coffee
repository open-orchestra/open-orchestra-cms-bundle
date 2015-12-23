TabElementFormView = OrchestraView.extend(

  extendView : [ 'submitAdmin' ]

  initialize: (options) ->
    @initializer options
    @loadTemplates [
      'OpenOrchestraBackofficeBundle:BackOffice:Underscore/backToList'
    ]
    return

  initializer: (options) ->
    @options = options
    @options.listUrl = appRouter.generateUrl('listEntities', entityType: options.entityType) if options.listUrl == undefined
    @options.formView = 'editEntityTab'
    @options.response = @options.html if true == @options.submitted
    @options.domContainer = @$el if !@options.submitted

  render: ->
    @options.domContainer.html @options.response
    @options.domContainer.append @renderTemplate('OpenOrchestraBackofficeBundle:BackOffice:Underscore/backToList',
        listUrl : @options.listUrl
    )
)
