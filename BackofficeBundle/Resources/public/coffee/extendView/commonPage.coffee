extendView['commonPage'] = {
  events:
    'click i.show-areas': 'showAreas'
    'click i.hide-areas': 'hideAreas'

  showAreas: ->
    $('.show-areas').hide()
    $('.hide-areas').show()
    $('.area-toolbar').addClass('shown')

  hideAreas: ->
    $('.hide-areas').hide()
    $('.show-areas').show()
    $('.area-toolbar').removeClass('shown')

  addConfigurationButton: ->
    view = new PageConfigurationButtonView(@addOption(
      viewContainer: @
    ))

  addAreaToView: (area) ->
    areaContainer = @$el.find('.ui-model-areas').first()
    areaElement = new Area
    areaElement.set area
    areaView = new AreaView(
      area: areaElement
      published: @options.pageConfiguration.attributes.status.published
      domContainer: areaContainer
    )
    areaContainer.addClass (if @options.node.get("bo_direction") is "h" then "bo-row" else "bo-column")
    return

}
