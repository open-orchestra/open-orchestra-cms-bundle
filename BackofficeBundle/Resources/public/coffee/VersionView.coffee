VersionView = OrchestraView.extend(
  tagName: "option"

  initialize: (options) ->
    @element = options.element
    @version = options.version
    @loadTemplates [
      "elementTitle"
      "elementChoice"
    ]
    return

  render: ->
    title = @renderTemplate('elementTitle',
      element: @element
    )
    $(@el).prepend @renderTemplate('elementChoice',
      title: title
      element: @element
      version: @version
      cid: @cid
    )
    return
)
