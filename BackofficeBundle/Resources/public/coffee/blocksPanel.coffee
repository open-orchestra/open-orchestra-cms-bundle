$(document).ready ->
  $('#blocks-panel-setting').click (e) ->
    $('div.blocks-panel').toggleClass "activate"
    $('#content .jarviswidget > div').toggleClass "activate"
    $(this).effect "highlight", {}, 500
    e.preventDefault()
    return
  return
