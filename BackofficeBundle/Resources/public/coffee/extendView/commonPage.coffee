extendView = extendView || {}
extendView['commonPage'] = {
  events:
    'click i.show-areas': 'showAreas'
    'click i.hide-areas': 'hideAreas'

  showAreas: ->
    $('.show-areas', @$el).hide()
    $('.hide-areas', @$el).show()
    $('.area-toolbar', @$el).addClass('shown')

  hideAreas: ->
    $('.hide-areas', @$el).hide()
    $('.show-areas', @$el).show()
    $('.area-toolbar', @$el).removeClass('shown')

  addConfigurationButton: () ->
    pageConfigurationButtonViewClass = appConfigurationView.getConfiguration(@options.entityType, 'addConfigurationButton')
    new pageConfigurationButtonViewClass(@addOption(
      viewContainer: @
      widget_index: 2
    ))

}
