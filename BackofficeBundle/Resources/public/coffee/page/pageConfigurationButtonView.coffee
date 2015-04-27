PageConfigurationButtonView = OrchestraView.extend(
  events:
    'click span': 'configurationPage'

  initialize: (options) ->
    @options = @reduce(options, [
      'node'
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
    $('.modal-title').text @options.node.get('name')
    options =
      url: @options.node.get('links')._self_form
      deleteurl: @options.node.get('links')._self_delete
      redirectUrl: appRouter.generateUrl "showNode",
        nodeId: @options.node.get('parent_id')
      confirmText: @options.viewContainer.$el.data('delete-confirm-txt')
      confirmTitle: @options.viewContainer.$el.data('delete-confirm-title')
    if @options.node.attributes.alias is ''
      $.extend options, inheritance: [ 'generateId' ]
    view = new adminFormView(options)
)
