#--[ MEDIA SELECTED ]--#
    
$(document).on "click", ".clear-media", (event) ->
  event.preventDefault()
  inputId = '#' + $(event.target).data('input')
  previewId = '#previewImage_' + $(event.target).data('input')
  $(inputId).val('')
  $(previewId).removeAttr('src')
