#--[ MEDIA SELECTED ]--#

$(document).on "click", ".media-select", (event) ->
  event.preventDefault()
  mediaModalContainer = $(event.target).parents(".mediaModalContainer")
  media = $('.superbox-img', $(event.target).parents(".superbox-list"))
  mediaSrc = media.attr('src')
  mediaId = media.data('id')
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
