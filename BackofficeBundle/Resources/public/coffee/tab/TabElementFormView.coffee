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

  render: ->
    @setElement $(@options.html)
    @$el.append @renderTemplate('OpenOrchestraBackofficeBundle:BackOffice:Underscore/backToList', listUrl : @options.listUrl)
)

