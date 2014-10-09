# TODO
# verify if it is useless with new smartAdmin version
# don't forget if you remove this file to remove the call into grunt.js
$(document).ready ->
  $('#demo-setting').click (e) ->
    $('div').toggleClass "activate"
    $(this).effect "highlight", {}, 500
    e.preventDefault()
    return
  return
