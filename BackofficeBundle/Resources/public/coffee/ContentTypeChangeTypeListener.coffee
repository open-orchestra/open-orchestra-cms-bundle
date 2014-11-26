$('html').on "change", ".content_type_change_type", (e) ->
  form = $(this).parents('form')
  url = form.attr('action')
  url = url + '?no_save=1'
  changedElement = $(this)
  optionId = changedElement.attr('id').replace(/type$/g, 'options')
  displayLoader('#' + optionId)
  form.ajaxSubmit
    url: url
    success: (response) ->
      $('#' + optionId).html $('#' + optionId, response).html()
