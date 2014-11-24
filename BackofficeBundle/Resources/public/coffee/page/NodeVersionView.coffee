NodeVersionView = OrchestraView.extend(
  tagName: "option"

  initialize: (options) ->
    @node = options.node
    @version = options.version
    @loadTemplates [
      "nodeTitle"
      "nodeChoice"
    ]
    return

  render: ->
    title = @renderTemplate('nodeTitle',
      node: @node
    )
    $(@el).prepend @renderTemplate('nodeChoice',
      title: title
      node: @node
      version: @version
      cid: @cid
    )
    return
)
