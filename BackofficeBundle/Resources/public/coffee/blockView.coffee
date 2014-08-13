BlockView = Backbone.View.extend(
  tagName: 'li'
  className: 'ui-model-blocks block'
  events:
    'click i.fa-cog': 'clickButton'
  initialize: (options) ->
    @block = options.block
    @height = options.height
    _.bindAll this, "render"
    @blockTemplate = _.template($('#blockView').html())
    return
  clickButton: (event) ->
    alert('click!')
  render: ->
    $(@el).attr('style', 'height:' + @height + '%').html @blockTemplate(
      block: @block
      height: @height
    )
    this
)
