extendView = extendView || {}
extendView['submitAdmin'] = {
  events:
    'click .submit_form': 'addEventOnSave'

  addEventOnSave: (event) ->
    viewContext = @
    viewClass = appConfigurationView.getConfiguration(viewContext.options.entityType, viewContext.options.formView)
    @buttonContext = $(event.target).parent() if event.originalEvent
    form = $(event.target).closest('form')
    if form.length == 0 && (clone = $(event.target).data('clone'))
      $('#' + clone).click()
    else
      if $("textarea.tinymce", form).length > 0
        tinymce.triggerSave()
      if !form.hasClass('HTML5validation')
        form.addClass('HTML5validation')
        form.submit ->
          event.preventDefault()
          form.ajaxSubmit
            url: form.data('action')
            context:
              button: viewContext.buttonContext
            statusCode:
              201: (response) ->
                widgetChannel.trigger 'element-created', viewContext

                displayRoute = $("#nav-" + viewContext.options.entityType).attr('href')
                Backbone.history.navigate(displayRoute, {trigger: true})
                viewClass = appConfigurationView.getConfiguration(viewContext.options.entityType, 'showFlashBag')
                new viewClass(viewContext.addOption(
                  html: response
                  domContainer: $('h1.page-title').parent()
                ))
                $(document).scrollTop 0
              200: (response) ->
                widgetChannel.trigger 'form-error', viewContext

                new viewClass(viewContext.addOption(
                  html: response
                  submitted: true
                ))
                if $('.tab-content .submit_form.btn-in-ribbon',@.$el).length > 0
                  $('.tab-content .submit_form.btn-in-ribbon',@.$el).removeClass('btn-in-ribbon')
                  OpenOrchestra.RibbonButton.ribbonFormButtonView.setFocusedView @, '.ribbon-form-button'
                $(document).scrollTop 0
              403: (response) ->
                displayRoute = OpenOrchestra.ForbiddenAccessRedirection[Backbone.history.fragment]
                if typeof displayRoute != 'undefined'
                  Backbone.history.navigate(displayRoute, {trigger: true})
          false
    return
}
