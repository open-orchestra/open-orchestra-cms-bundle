$(document).on "click", ".blocks-panel-setting", (event) ->
  $(this).parent().toggleClass "activate"
  $('#content .jarviswidget > div').toggleClass "panel-activate"
  $(this).effect "highlight", {}, 500
  event.preventDefault()
  return
