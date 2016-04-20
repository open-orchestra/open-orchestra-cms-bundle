extendView = extendView || {}
extendView['commonPage'] = {
  events:
    'click i.show-areas': 'showAreas'
    'click i.hide-areas': 'hideAreas'

  ###
   * Show areas
  ###
  showAreas: ->
    $('.show-areas', @$el).hide()
    $('.hide-areas', @$el).show()
    $('.area-toolbar', @$el).addClass('shown')

  ###
   * Hide areas
  ###
  hideAreas: ->
    $('.hide-areas', @$el).hide()
    $('.show-areas', @$el).show()
    $('.area-toolbar', @$el).removeClass('shown')

  ###
   * Add layout button (edit and delete)
  ###
  addPageLayoutButton: () ->
    pageLayoutButtonViewClass = appConfigurationView.getConfiguration(@options.entityType, 'addPageLayoutButton')
    new pageLayoutButtonViewClass(@addOption(
      viewContainer: @
      widget_index: 2
      deleteUrl: @options.configuration.get('links')._self_delete
      confirmText: @$el.data('delete-confirm-txt')
      confirmTitle: @$el.data('delete-confirm-title')
      redirectUrl: @options.redirectUrl
    ))
}
