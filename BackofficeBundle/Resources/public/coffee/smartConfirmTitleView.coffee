SmartConfirmTitleView = OrchestraView.extend(
  tagName: "span"

  initialize: (options) ->
    @logo = options.logo
    @titleWhite = options.titleWhite
    @titleColorized = options.titleColorized
    @loadTemplates [
      "smartConfirmTitle"
    ]
    return

  render: ->
    @renderTemplate('smartConfirmTitle',
      titleWhite: @titleWhite
      titleColorized: @titleColorized
      logo: @logo
    )
    return
)
