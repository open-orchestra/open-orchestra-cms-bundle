extendView = extendView || {}
extendView['submitAdmin'] = {
  events:
    'click .submit_form': 'addEventOnSave'

  addEventOnSave: (event) ->
    viewContext = @
    viewClass = appConfigurationView.getConfiguration(viewContext.options.entityType, viewContext.options.formView)
    @button = $(event.target).parent() if event.originalEvent
    form = $(event.target).closest('form')
    if form.length == 0 && (clone = $(event.target).data('clone'))
      $('#' + clone).click()
    else
      if $("textarea.tinymce", form).length > 0
        $('textarea.tinymce').each ->
          $(@).sync()
      #  tinymce.triggerSave()
      if !form.hasClass('HTML5validation')
        form.addClass('HTML5validation')
        form.submit ->
          event.preventDefault()
          form.ajaxSubmit
            context:
              button: viewContext.button
            statusCode:
              201: (response) ->
                displayRoute = $("#nav-" + viewContext.options.entityType).attr('href')
                Backbone.history.navigate(displayRoute, {trigger: true})
                viewClass = appConfigurationView.getConfiguration(viewContext.options.entityType, 'showFlashBag')
                new viewClass(viewContext.addOption(
                  html: response
                  domContainer: $('h1.page-title').parent()
                ))
              200: (response) ->
                new viewClass(viewContext.addOption(
                  html: response
                  submitted: true
                ))
          false
    return
}
