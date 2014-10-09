$(document).ready ->
  $('#blocks-panel-setting').click (e) ->
    $('div').toggleClass "activate"
    $(this).effect "highlight", {}, 500
    e.preventDefault()
    return
  return
