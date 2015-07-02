checkContentTypeId = (selectorInput) ->

  inputId = $(selectorInput)
  form = $(selectorInput).parents('form')

  dataMessage = form.find("#content_type_fields").data("prototype-callback-error-message")
  errorMessage = "<div class=\"callback-content-type-alert alert alert-warning\" role=\"alert\">" + dataMessage + "</div>"
  form.children(".callback-content-type-alert").remove()

  if not inputId.val().length > 0
    # display error
    form.prepend(errorMessage);
    form.find("#content_type_fields button.prototype-add").attr('disabled', 'disabled')
    return false
  else
    form.find("#content_type_fields button.prototype-add").removeAttr('disabled')
  return true
