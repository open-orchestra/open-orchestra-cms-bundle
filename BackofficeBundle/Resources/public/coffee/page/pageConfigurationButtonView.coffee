PageConfigurationButtonView = OrchestraView.extend(
  events:
    'click span': 'configurationPage'

  initialize: (options) ->
    @options = @reduceOption(options, [
      'configuration'
      'viewContainer'
    ])
    @loadTemplates [
      "OpenOrchestraBackofficeBundle:BackOffice:Underscore/widgetPageConfigurationButton"
    ]
    return

  render: ->
    @setElement @renderTemplate('OpenOrchestraBackofficeBundle:BackOffice:Underscore/widgetPageConfigurationButton')
    addCustomJarvisWidget(@$el)
    return

  configurationPage: () ->
    $('.modal-title').text @options.configuration.get('name')
    options =
      url: @options.configuration.get('links')._self_form
      deleteUrl: @options.configuration.get('links')._self_delete
      redirectUrl: appRouter.generateUrl "showNode",
        nodeId: @options.configuration.get('parent_id')
      confirmText: @options.viewContainer.$el.data('delete-confirm-txt')
      confirmTitle: @options.viewContainer.$el.data('delete-confirm-title')
    if @options.configuration.attributes.alias is ''
      $.extend options, extendView: [ 'generateId' ]
    new adminFormView(options)
)
