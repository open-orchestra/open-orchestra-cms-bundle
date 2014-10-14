#--[ OPEN MEDIA MODAL ]--#

$(document).on "click", ".mediaModalOpen", (event) ->
  button = event.currentTarget
  modalId = $(button).data("target")
  mediaModal = $("#" + modalId)
  
  orchestraModal = $("#OrchestraBOModal")
  
  mediaModalDialog = mediaModal.find(".modal-dialog:first")
  left = (mediaModalDialog.width() - orchestraModal.width() - $.scrollbarWidth()) / 2
  
  mediaModalBody = mediaModal.find(".modal-body:first")
  
  mediaModal.css "top", "-88px"
  mediaModal.css "left", left + "px"
  mediaModal.css "width", orchestraModal.css("width")
  mediaModal.css "height", orchestraModal.css("height")
  mediaModalBody.css "min-height", (mediaModal.height() - 120) + "px"
  
  $('#' + modalId + ' .modal-body-content').empty()
  
  mediaModal.modal "show"
  
  view = new mediaFormView(
    menuUrl: $('#' + modalId + ' .modal-body-menu').data('url'),
    el: '#' + modalId;
  )
  
  return


#--[ CLOSE MEDIA MODAL ]--#

$(document).on "click", ".mediaModalClose", (event) ->
  button = event.currentTarget
  modalId = $(button).data("target")
  mediaModal = $("#" + modalId)
  mediaModal.modal "hide"
  mediaModal.css "width", "0px"
  mediaModal.css "height", "0px"
  return


#--[ FOLDER CLICKED ]--#

$(document).on "click", ".media-modal-menu-folder", (event) ->
  modalId = $(event.target).parents(".mediaModalContainer").find('.fade').attr('id')
  displayLoader("#" + modalId + " .modal-body-content")
  tableViewLoad($(event.target), "#" + modalId + " .modal-body-content")


#--[ MEDIA SELECTED ]--#

$(document).on "click", ".mediaModalContainer .ajax-select", (event) ->
  event.preventDefault()
  mediaModalContainer = $(event.target).parents(".mediaModalContainer")
  mediaId = $(event.target).attr('href')
  $('#' + mediaModalContainer.data('input')).val(mediaId)
  modalId = mediaModalContainer.find('.mediaModalClose').click()

