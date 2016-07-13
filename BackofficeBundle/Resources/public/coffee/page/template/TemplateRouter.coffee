###*
 * Router template
###
((router) ->

  ###*
   * show template
  ###
  router.route 'template/show/:templateId', 'showTemplate', (templateId) ->
    @initDisplayRouteChanges '#nav-template-' + templateId
    $.ajax
      type: "GET"
      url: $('#nav-template-' + templateId).data('url')
      success: (response) ->
        template = new penOrchestra.Page.Template.Template
        template.set response
        templateViewClass = appConfigurationView.getConfiguration('template', 'showTemplate')
        new templateViewClass(
          template: template
          domContainer: $('#content')
        )
        return
    return

) window.appRouter
