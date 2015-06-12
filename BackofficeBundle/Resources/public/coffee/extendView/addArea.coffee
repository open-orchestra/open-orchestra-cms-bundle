extendView = extendView || {}
extendView['addArea'] = {
  addAreasToView: (areas) ->
    @areaContainer = @$el.find('.ui-model-areas').first()
    for index of areas
      @addAreaToView(areas[index])
    refreshUl @areaContainer

  addAreaToView: (area) ->
    areaElement = new Area
    areaElement.set area
    areaViewClass = appConfigurationView.getConfiguration('area', 'addArea')
    new areaViewClass(
      area: areaElement
      configuration: areaElement
      published: @options.published
      domContainer: @areaContainer
    )
    @areaContainer.addClass (if @options.configuration.get("bo_direction") is "h" then "bo-row" else "bo-column")
    return
}
