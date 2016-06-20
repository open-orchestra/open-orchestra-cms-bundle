###*
 * @class DuplicateView
###
class DuplicateView extends OrchestraView

  events:
    'click a': 'duplicateElement'

  ###*
   * required options
   * {
   *   domContainer: {Object}
   *   currentDuplicate: {Object}
   *   tableId : {string}
   * }
   *
   * @param {Object} options
  ###
  initialize: (options) ->
    @options = options
    @loadTemplates [
      "OpenOrchestraBackofficeBundle:BackOffice:Underscore/widgetDuplicate"
    ]
    return

  ###*
   * Render button duplicate
  ###
  render: ->
    @setElement @renderTemplate('OpenOrchestraBackofficeBundle:BackOffice:Underscore/widgetDuplicate',
      text: @options.domContainer.data('text')
    )
    @options.domContainer.append @$el
    return

  ###*
   * Event click button duplicate
   *
   * @param {event} event
  ###
  duplicateElement: (event) ->
    event.preventDefault()
    displayLoader(@$el)
    parameter = appRouter.addParametersToRoute(
      language: @options.currentDuplicate.language
    )
    delete parameter.version if parameter.version
    redirectUrl = appRouter.generateUrl(@options.currentDuplicate.path, parameter)
    view = @
    $.ajax
      url: @options.currentDuplicate.self_duplicate
      method: 'POST'
      success: ->
        if (redirectUrl != Backbone.history.fragment)
          Backbone.history.navigate(redirectUrl, {trigger: true})
        else
          Backbone.history.loadUrl()
      error: ->
        view.$el.remove()
        view.render()
    return
