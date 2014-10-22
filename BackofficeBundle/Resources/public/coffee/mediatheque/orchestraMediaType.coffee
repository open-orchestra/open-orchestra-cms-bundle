#--[ MEDIA SELECTED ]--#

$(document).on "dblclick", ".mediaModalContainer img.selectable", (event) ->
  event.preventDefault()
  mediaModalContainer = $(event.target).parents(".mediaModalContainer")
  mediaId = $(event.target).data('id')
  $('#' + mediaModalContainer.data('input')).val(mediaId)
  modalId = mediaModalContainer.find('.mediaModalClose').click()
