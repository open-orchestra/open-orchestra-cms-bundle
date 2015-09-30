extendView = extendView || {}
extendView['contentTypeChange'] = {
  events:
    'change .content_type_change_type': 'changeContentTypeChange'

  changeContentTypeChange: (event) ->
    event.preventDefault()
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
        defaultValueField = if $('#' + defaultValueId, response).length > 0  then $('#' + defaultValueId, response).closest( ".form-group" ).html() else ""
        defaultValueViewClass = appConfigurationView.getConfiguration('fieldType', 'addFieldOptionDefaultValue')
        defaultValueView = new defaultValueViewClass(html: defaultValueField)

        $('#' + optionId).html $('#' + optionId, response).html()
        formGroupDefaultValue.html defaultValueView.render().$el
        formGroupDefaultValue.show()

        widgetChannel.trigger 'ready', defaultValueView
        if $('#' + defaultValueId).hasClass('tinymce')
            tinymce.editors = []
            activateTinyMce(defaultValueView, $('#' + defaultValueId))
}
