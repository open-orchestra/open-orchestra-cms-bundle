###*
 * Router template flex
###
((router) ->

  ###*
   * show template
  ###
  router.route 'template-flex/show/:templateId', 'showTemplateFlex', (templateId) ->
    @initDisplayRouteChanges '#nav-template-' + templateId
    $.ajax
      type: "GET"
      url: $('#nav-template-' + templateId).data('url')
      success: (response) ->
        template = new TemplateModel
        template.set response
        templateViewClass = appConfigurationView.getConfiguration('template-flex', 'showTemplateFlex')
        new templateViewClass(
          template: template
          domContainer: $('#content')
        )
        return
    return

) window.appRouter
