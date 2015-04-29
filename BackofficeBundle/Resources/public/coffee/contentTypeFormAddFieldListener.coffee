checkContentTypeId = ->
  inputId = $("#content_type_contentTypeId").get(0)
  form = $(inputId).parents('form')
  dataMessage = $("#content_type_fields").data("prototype-callback-error-message");
  errorMessage = "<div class=\"callback-content-type-alert alert alert-danger\" role=\"alert\">" + dataMessage + "</div>"
  $(".callback-content-type-alert").remove()
  if not $(inputId).val().length > 0
    # display error
    $(form).prepend(errorMessage);
    return false
  return true
