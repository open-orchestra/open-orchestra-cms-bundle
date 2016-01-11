extendView = extendView || {}
extendView['addArea'] = {
  addAreasToView: (areas) ->
    @areaContainer = @$el.find('.ui-model-areas').first()
    @areaContainer.html ''
    for index of areas
      @addAreaToView(areas[index])
    refreshUl @areaContainer

  addAreaToView: (area) ->
    areaElement = new Area
    areaElement.set area
    areaViewClass = appConfigurationView.getConfiguration(@options.entityType, 'addArea')
    new areaViewClass(
      area: areaElement
      configuration: areaElement
      editable: @options.editable
      domContainer: @areaContainer
    )
    console.log @options.configuration
    @areaContainer.addClass (if @options.configuration.get("bo_direction") is "h" then "bo-row" else "bo-column")
    return
}
