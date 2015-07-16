extendView = extendView || {}
extendView['submitAdmin'] = {
  events:
    'click .submit_form': 'addEventOnSave'

  addEventOnSave: (event) ->
    tinymce.triggerSave()
    viewContext = @
    viewClass = appConfigurationView.getConfiguration(@options.entityType, @options.formView)
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
              new viewClass(viewContext.addOption(
                html: response
                submitted: true
              ))
            error: (response) ->
              new viewClass(viewContext.addOption(
                html: response.responseText
              ))
          false
    return
}
