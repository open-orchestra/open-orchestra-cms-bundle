NodeVersionView = Backbone.View.extend(
  tagName: "option"
  el: '#versions'
  initialize: (options) ->
    @node = options.node
    @version = options.version
    @nodeTitle = _.template($("#nodeTitle").html())
    @nodeChoice = _.template($("#nodeChoice").html())
    return
  render: ->
    title = @nodeTitle(
      node: @node
    )
    $(@el).prepend @nodeChoice(
      title: title
      node: @node
      version: @version
      cid: @cid
    )
    return
)
