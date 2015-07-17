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
    default_valueId = target.attr('id').replace(/type$/g, 'default_value')
    displayLoader('#' + optionId)
    $('#' + default_valueId).closest( ".form-group" ).hide()
    $('#' + default_valueId).val('')
    form.ajaxSubmit
      type: 'PATCH'
      url: url
      success: (response) ->
        $('#' + optionId).html $('#' + optionId, response).html()
        default_value_field = if $('#' + default_valueId, response).length > 0  then $('#' + default_valueId, response).closest( ".form-group" ).html() else ""
        $('#' + default_valueId).closest( ".form-group" ).html default_value_field
        $('#' + default_valueId).closest( ".form-group" ).show()
        activateTinyMce(viewContext, $('#' + default_valueId)) if $('#' + default_valueId).hasClass('tinymce')
}
