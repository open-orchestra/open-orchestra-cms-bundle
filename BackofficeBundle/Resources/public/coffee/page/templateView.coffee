TemplateView = OrchestraView.extend(
  el: '#content'

  events:
    'click i#none' : 'clickButton'

  initialize: (options) ->
    @template = options.template
    @events['click span.' + @cid] = 'clickButton'
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
      confirmtitle: $(".delete-confirm-title-"+@template.cid).text()
    )

  render: ->
    $(@el).html @renderTemplate('templateView',
      template: @template
    )
    $('.js-widget-title', @$el).html $('#generated-title', @$el).html()
    @addConfigurationButton()
    for area of @template.get('areas')
      @addAreaToView(@template.get('areas')[area])
    return

  addAreaToView: (area) ->
    domContainer = @$el.find('div[role="container"] > div > .ui-model-areas')
    areaElement = new Area
    areaElement.set area
    areaView = new AreaView(
      area: areaElement,
      domContainer: domContainer
      viewContainer: @
    )
    domContainer.addClass (if @template.get("bo_direction") is "h" then "bo-row" else "bo-column")
    $(".ui-model-areas", @$el).each ->
      refreshUl $(this)
    return

  showAreas: ->
    $('.show-areas').hide()
    $('.hide-areas').show()
    $('.area-toolbar').addClass('shown')

  hideAreas: ->
    $('.hide-areas').hide()
    $('.show-areas').show()
    $('.area-toolbar').removeClass('shown')

  addConfigurationButton: ->
    cid = @cid
    view = new PageConfigurationButtonView(
      cid: cid
    )
)
