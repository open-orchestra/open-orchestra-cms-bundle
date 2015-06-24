extendView = extendView || {}
extendView['submitAdmin'] = {
  events:
    'click .submit_form': 'addEventOnSave'
    'submit': 'addEventOnForm'

  addEventOnSave: (event) ->
    tinymce.triggerSave();
    viewContext = @
    viewClass = appConfigurationView.getConfiguration(@options.entityType, @options.formView)
    button = $(event.target).parent()
    $('form', @$el).ajaxSubmit
      context:
        button: button
      success: (response) ->
        new viewClass(viewContext.addOption(
          html: response
          submitted: true
        ))
      error: (response) ->
        new viewClass(viewContext.addOption(
          html: response.responseText
        ))
}
