#--[ MEDIA SELECTED ]--#

$(document).on "click", ".mediaModalContainer .ajax-select", (event) ->
  event.preventDefault()
  mediaModalContainer = $(event.target).parents(".mediaModalContainer")
  mediaId = $(event.target).attr('href')
  $('#' + mediaModalContainer.data('input')).val(mediaId)
  modalId = mediaModalContainer.find('.mediaModalClose').click()
