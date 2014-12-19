SmartConfirmTitleView = OrchestraView.extend(

  initialize: (options) ->
    @logo = options.logo
    @titleColorized = options.titleColorized
    @loadTemplates [
      "smartConfirmTitle"
    ]
    return

  render: ->
    return @renderTemplate('smartConfirmTitle',
      titleColorized: @titleColorized
      logo: @logo
    )
)
