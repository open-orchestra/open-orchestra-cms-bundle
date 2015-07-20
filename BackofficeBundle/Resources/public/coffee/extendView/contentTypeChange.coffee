extendView = extendView || {}
extendView['contentTypeChange'] = {
  events:
    'change .content_type_change_type': 'changeContentTypeChange'

  changeContentTypeChange: (event) ->
    event.preventDefault()
    viewContext = @
    target = $(event.currentTarget)
    form = target.parents('form')
    url = form.attr('action')
    url = url + '?no_save=1'
    optionId = target.attr('id').replace(/type$/g, 'options')
    defaultValueId = target.attr('id').replace(/type$/g, 'default_value')
    defaultValueField = $('#' + defaultValueId)
    formGroupDefaultValue = defaultValueField.closest( ".form-group" )

    displayLoader('#' + optionId)
    formGroupDefaultValue.hide()
    defaultValueField.val('')

    form.ajaxSubmit
      type: 'PATCH'
      url: url
      success: (response) ->
        $('#' + optionId).html $('#' + optionId, response).html()
        default_value_field = if $('#' + defaultValueId, response).length > 0  then $('#' + defaultValueId, response).closest( ".form-group" ).html() else ""

        formGroupDefaultValue.html default_value_field
        formGroupDefaultValue.show()
        activateTinyMce(viewContext, $('#' + defaultValueId)) if $('#' + defaultValueId).hasClass('tinymce')
}
