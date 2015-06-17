PageConfigurationButtonView = OrchestraView.extend(
  events:
    'click span': 'configurationPage'

  initialize: (options) ->
    @options = @reduceOption(options, [
      'configuration'
      'viewContainer'
      'entityType'
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
    options =
      url: @options.configuration.get('links')._self_form
      deleteUrl: @options.configuration.get('links')._self_delete
      redirectUrl: appRouter.generateUrl "showNode",
        nodeId: @options.configuration.get('parent_id')
      confirmText: @options.viewContainer.$el.data('delete-confirm-txt')
      confirmTitle: @options.viewContainer.$el.data('delete-confirm-title')
      extendView: [ 'deleteTree' ]
      title: @options.configuration.get('name')
    if @options.configuration.attributes.alias is ''
      $.extend options, extendView: [ 'generateId']
    adminFormViewClass = appConfigurationView.getConfiguration(@options.entityType, 'showAdminForm')
    new adminFormViewClass(options)
)
