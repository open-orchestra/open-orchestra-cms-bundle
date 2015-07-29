extendView = extendView || {}
extendView['submitAdmin'] = {
  events:
    'click .submit_form': 'addEventOnSave'

  addEventOnSave: (event) ->
    tinymce.triggerSave()
    viewContext = @
    viewClass = appConfigurationView.getConfiguration(viewContext.options.entityType, viewContext.options.formView)
    @button = $(event.target).parent() if event.originalEvent
    form = $(event.target).closest('form')
    if form.length == 0 && (clone = $(event.target).data('clone'))
      $('#' + clone).click()
    else
      if !form.hasClass('HTML5validation')
        form.addClass('HTML5validation')
        form.submit ->
          event.preventDefault()
          form.ajaxSubmit
            context:
              button: viewContext.button
            success: (response) ->
              if($(".formCreation").data('type') != "listableCreation")
                new viewClass(viewContext.addOption(
                  html: response
                  submitted: true
                ))
              else
                displayRoute = $("#nav-" + viewContext.options.entityType).attr('href')
                Backbone.history.navigate(displayRoute, {trigger: true})
                viewClass = appConfigurationView.getConfiguration(viewContext.options.entityType, 'showFlashBag')
                new viewClass(viewContext.addOption(
                  html: response
                  domContainer: $('h1.page-title').parent()
                ))
            error: (response) ->
              new viewClass(viewContext.addOption(
                html: response.responseText
              ))
          false
    return
}
