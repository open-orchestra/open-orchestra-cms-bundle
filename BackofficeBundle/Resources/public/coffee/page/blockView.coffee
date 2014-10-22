BlockView = Backbone.View.extend(
  tagName: 'li'
  className: 'ui-model-blocks'
  events:
    'click div.block-param': 'paramBlock'
  initialize: (options) ->
    @block = options.block
    @displayClass = options.displayClass
    _.bindAll this, "render"
    @blockTemplate = _.template($('#blockView').html())
    return
  paramBlock: (event) ->
    $('.modal-title').text 'Please wait ...'
    view = new adminFormView(url: @block.get('links')._self_form)
  render: ->
    $(@el).addClass(@displayClass).html @blockTemplate(
      block: @block
    )
    this
)
