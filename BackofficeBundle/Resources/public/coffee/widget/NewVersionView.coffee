###*
 * @class NewVersionView
###
class NewVersionView extends OrchestraView

  events:
    'click a': 'newVersionElement'

  ###*
   * required options
   * {
   *   domContainer: {Object}
   *   currentNewVersion: {Object}
   *   tableId : {string}
   * }
   *
   * @param {Object} options
  ###
  initialize: (options) ->
    @options = options
    @loadTemplates [
      "OpenOrchestraBackofficeBundle:BackOffice:Underscore/widgetNewVersion"
    ]
    return

  ###*
   * Render button new version
  ###
  render: ->
    @setElement @renderTemplate('OpenOrchestraBackofficeBundle:BackOffice:Underscore/widgetNewVersion',
      text: @options.domContainer.data('text')
    )
    @options.domContainer.append @$el
    return

  ###*
   * Event click button new version
   *
   * @param {event} event
  ###
  newVersionElement: (event) ->
    event.preventDefault()
    displayLoader(@$el)
    parameter = appRouter.addParametersToRoute(
      language: @options.currentNewVersion.language
    )
    delete parameter.version if parameter.version
    redirectUrl = appRouter.generateUrl(@options.currentNewVersion.path, parameter)
    view = @
    $.ajax
      url: @options.currentNewVersion.self_new_version
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
