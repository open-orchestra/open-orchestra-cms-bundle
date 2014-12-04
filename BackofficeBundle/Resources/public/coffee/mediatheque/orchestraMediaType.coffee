#--[ MEDIA SELECTED ]--#

$(document).on "dblclick", ".mediaModalContainer img.selectable", (event) ->
  event.preventDefault()
  mediaModalContainer = $(event.target).parents(".mediaModalContainer")
  mediaSrc = $(event.target).attr('src')
  mediaId = $(event.target).data('id')
  inputId = '#' + mediaModalContainer.data('input')
  previewId = '#previewImage_' + mediaModalContainer.data('input')
  $(inputId).val(mediaId)
  $(previewId).attr('src', mediaSrc)
  modalId = mediaModalContainer.find('.mediaModalClose').click()

$(document).on "click", ".clear-media", (event) ->
  event.preventDefault()
  inputId = '#' + $(event.target).data('input')
  previewId = '#previewImage_' + $(event.target).data('input')
  $(inputId).val('')
  $(previewId).removeAttr('src')
