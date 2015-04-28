extendView = extendView || {}
extendView['addArea'] = {
  addAreasToView: (areas) ->
    @areaContainer = @$el.find('.ui-model-areas').first()
    for index of areas
      @addAreaToView(areas[index])
    refreshUl @areaContainer

  addAreaToView: (area) ->
    status = @options.configuration.attributes.status
    published = if typeof status != 'undefined' then status.published else false
    areaElement = new Area
    areaElement.set area
    areaView = new AreaView(
      area: areaElement
      configuration: areaElement
      published: published
      domContainer: @areaContainer
    )
    @areaContainer.addClass (if @options.configuration.get("bo_direction") is "h" then "bo-row" else "bo-column")
    return

}
