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
  
  mediaModal.modal "show"
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
