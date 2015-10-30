FormCollectionView = OrchestraView.extend(
  initialize: (options) ->
    @options = options
    @loadTemplates [
      'OpenOrchestraBackofficeBundle:BackOffice:Underscore/groupTreeForm',
    ]

  render: ->
    for role in @options.roles
      @options.domContainer.append @renderTemplate('OpenOrchestraBackofficeBundle:BackOffice:Underscore/groupTreeForm',
        role: role
      )
)