SmartConfirmTitleView = OrchestraView.extend(

  initialize: (options) ->
    @logo = options.logo
    @titleColorized = options.titleColorized
    return

  render: ->
    return @renderTemplate('smartConfirmTitle',
      titleColorized: @titleColorized
      logo: @logo
    )
)
