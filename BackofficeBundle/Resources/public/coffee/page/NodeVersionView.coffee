NodeVersionView = Backbone.View.extend(
  tagName: "option"
  el: '#selectbox'
  events:
    'click i#none': 'clickOption'
  initialize: (options) ->
    @node = options.node
    @version = options.version
    @nodeTitle = _.template($("#nodeTitle").html())
    @nodeChoice = _.template($("#nodeChoice").html())
    key = 'click .version-select-' + @cid
    @events[key] = 'clickOption'
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
  clickOption: ->
    Backbone.router.navigate('#node/show/' + @node.get('node_id') + '/' + @node.get('version'))
    return
)
