checkContentTypeId = (selectorInput) ->

  inputId = $(selectorInput)
  form = $(selectorInput).parents('form')

  dataMessage = form.find("#content_type_fields").data("prototype-callback-error-message")
  errorMessage = "<div class=\"callback-content-type-alert alert alert-danger\" role=\"alert\">" + dataMessage + "</div>"
  form.children(".callback-content-type-alert").remove()

  if not inputId.val().length > 0
    # display error
    form.prepend(errorMessage);
    return false
  return true
