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
        tinymce.triggerSave()
      if !form.hasClass('HTML5validation')
        form.addClass('HTML5validation')
        #form.attr 'action', form.data('action')
        form.submit()
    return
}
