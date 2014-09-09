BlockView = Backbone.View.extend(
  tagName: 'li'
  className: 'ui-model-blocks'
  events:
    'click i.fa-cog': 'clickButton'
  initialize: (options) ->
    @block = options.block
    @height = options.height
    @direction = options.direction || 'height'
    @displayClass = (if @direction is "width" then "inline" else "block")
    _.bindAll this, "render"
    @blockTemplate = _.template($('#blockView').html())
    return
  clickButton: (event) ->
    $('.modal-title').text @block.get('component')
    displayLoader('.modal-body')
    $.ajax
      url: @block.get('links')._self_form
      method: 'GET'
      success: (response) ->
        view = new adminFormView(html: response)
  render: ->
    $(@el).attr('style', @direction + ':' + @height + '%').addClass(@displayClass).html @blockTemplate(
      block: @block
      height: @height
    )
    this
)
