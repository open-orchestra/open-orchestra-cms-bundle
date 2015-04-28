PageConfigurationButtonView = OrchestraView.extend(
  events:
    'click span': 'configurationPage'

  initialize: (options) ->
    @options = @reduceOption(options, [
      'pageConfiguration'
      'viewContainer'
    ])
    @loadTemplates [
      "widgetPageConfigurationButton"
    ]
    return

  render: ->
    @setElement @renderTemplate('widgetPageConfigurationButton')
    addCustomJarvisWidget(@$el)
    return

  configurationPage: () ->
    $('.modal-title').text @options.pageConfiguration.get('name')
    options =
      url: @options.pageConfiguration.get('links')._self_form
      deleteurl: @options.pageConfiguration.get('links')._self_delete
      redirectUrl: appRouter.generateUrl "showNode",
        nodeId: @options.pageConfiguration.get('parent_id')
      confirmText: @options.viewContainer.$el.data('delete-confirm-txt')
      confirmTitle: @options.viewContainer.$el.data('delete-confirm-title')
    if @options.pageConfiguration.attributes.alias is ''
      $.extend options, extendView: [ 'generateId' ]
    view = new adminFormView(options)
)
