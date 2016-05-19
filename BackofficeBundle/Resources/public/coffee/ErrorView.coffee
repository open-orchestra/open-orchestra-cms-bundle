###*
 * @namespace OpenOrchestra
###
window.OpenOrchestra or= {}

###*
 * @class ErrorView
###
class OpenOrchestra.ErrorView extends OrchestraView

  ###*
   * initialize
   *
   * @param {Object} option
  ###
  initialize: (options) ->
    @options = @reduceOption(options, [
      'domContainer'
    ])
    @loadTemplates [
        'OpenOrchestraBackofficeBundle:BackOffice:Underscore/errorView'
    ]
    return

  ###*
   * render
  ###
  render: ->
    @setElement @renderTemplate('OpenOrchestraBackofficeBundle:BackOffice:Underscore/errorView', @options)
    @options.domContainer.html @$el
    return

jQuery ->
  appConfigurationView.setConfiguration('all', 'showError', OpenOrchestra.ErrorView)

((router) ->
  router.route 'error', 'showError', ->
    jQuery ->
      viewClass = appConfigurationView.getConfiguration('all', 'showError')
      new viewClass(
        domContainer: $('#content')
      )
    return

) window.appRouter
