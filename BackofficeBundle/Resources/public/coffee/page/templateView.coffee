TemplateView = OrchestraView.extend(
  el: '#content'

  events:
    'click i#none' : 'clickButton'

  initialize: (options) ->
    @template = options.template
    @events['click i.' + @template.cid] = 'clickButton'
    @events['click i.show-areas'] = 'showAreas'
    @events['click i.hide-areas'] = 'hideAreas'
    _.bindAll this, "render", "addAreaToView", "clickButton"
    @loadTemplates [
      "templateView"
      "areaView"
    ]
    return

  clickButton: (event) ->
    $('.modal-title').text @template.get('name')
    view = new adminFormView(
      url: @template.get('links')._self_form
      deleteurl: @template.get('links')._self_delete
      confirmtext: $(".delete-confirm-txt-"+@template.cid).text()
    )

  render: ->
    $(@el).html @renderTemplate('templateView',
      template: @template
    )
    $('.js-widget-title', @$el).html $('#generated-title', @$el).html()
    for area of @template.get('areas')
      @addAreaToView(@template.get('areas')[area])
    return

  addAreaToView: (area) ->
    areaElement = new Area
    areaElement.set area
    areaView = new AreaView(
      area: areaElement,
      displayClass: (if @template.get("bo_direction") is "h" then "bo-row" else "bo-column")
      el: this.$el.find('div[role="container"]').children('div').children('ul.ui-model-areas')
    )
    $("ul.ui-model-areas", @$el).each ->
      refreshUl $(this)
    return

  showAreas: ->
    $('.show-areas').hide()
    $('.hide-areas').show()
    $('div.toolbar-layer.area-toolbar').addClass('shown')

  hideAreas: ->
    $('.hide-areas').hide()
    $('.show-areas').show()
    $('div.toolbar-layer.area-toolbar').removeClass('shown')
)
