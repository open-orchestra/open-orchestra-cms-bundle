VersionView = OrchestraView.extend(
  tagName: "option"

  initialize: (options) ->
    @options = options
    @loadTemplates [
      "elementTitle"
      "elementChoice"
    ]
    return

  render: ->
    title = @renderTemplate('elementTitle',
      element: @options.element
    )
    $(@el).prepend @renderTemplate('elementChoice',
      title: title
      element: @options.element
      version: @options.version
      cid: @cid
    )
    return
)
