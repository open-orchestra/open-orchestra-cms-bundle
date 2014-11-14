#--[ MEDIA SELECTED ]--#

$(document).on "dblclick", ".mediaModalContainer img.selectable", (event) ->
  event.preventDefault()
  mediaModalContainer = $(event.target).parents(".mediaModalContainer")
  mediaSrc = $(event.target).attr('src')
  mediaId = $(event.target).data('id')
  $('#' + mediaModalContainer.data('input')).val(mediaId)
  $('img#previewImage').attr('src', mediaSrc)
  modalId = mediaModalContainer.find('.mediaModalClose').click()
