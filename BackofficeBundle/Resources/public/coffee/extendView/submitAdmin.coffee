extendView = extendView || {}
extendView['submitAdmin'] = {
  events:
    'click .submit_form': 'addEventOnSave'

  addEventOnSave: (event) ->
    event.preventDefault()
    viewContext = @
    viewClass = appConfigurationView.getConfiguration(viewContext.options.entityType, 'showOrchestraModal')
    $("form", @$el).ajaxSubmit
      context:
        button: $(".submit_form", viewContext.$el).parent()
      statusCode:
        200: (response) ->
          view = new viewClass(viewContext.addOption(
            body: response
            title: viewContext.options.title
            entityType: viewContext.options.entityType
          ))
          if $('#node_nodeId', view.$el).length > 0
            displayRoute = appRouter.generateUrl "showNode",
              nodeId: $('#node_nodeId', view.$el).val()
          else if $('#template_templateId', view.$el).length > 0
            displayRoute = appRouter.generateUrl "showTemplate",
              templateId: $('#template_templateId', view.$el).val()
          else
            displayRoute = Backbone.history.fragment
            Backbone.history.loadUrl(displayRoute)
          displayMenu(displayRoute)
        400: (response) ->
          new viewClass(viewContext.addOption(
            body: response.responseText
            title: viewContext.options.title
            entityType: viewContext.options.entityType
          ))
}
