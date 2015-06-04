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
    displayLoader('#' + optionId)
    form.ajaxSubmit
      type: 'PATCH'
      url: url
      success: (response) ->
        $('#' + optionId).html $('#' + optionId, response).html()
}
