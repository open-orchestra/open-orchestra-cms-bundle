# CLOSE MEDIA MODAL

$(document).on "click", ".mediaModalClose", (event) ->
  mediaModal = $("#" + $(event.currentTarget).data("target"))
  mediaModal.modal "hide"
  mediaModalContainer = mediaModal.parent()
  
  mediaModalContainer.detach().appendTo($('#' + mediaModalContainer.data('input')).parent())
  
  mediaModal.css "width", "0px"
  mediaModal.css "height", "0px"
  
  return



# FOLDER CLICKED

$(document).on "click", ".media-modal-menu-folder", (event) ->
  modalId = $(event.target).parents(".mediaModalContainer").find(".fade").attr("id")
  displayLoader "#" + modalId + " .modal-body-content"
  GalleryLoad $(event.target), "#" + modalId + " .modal-body-content"



# NEW FOLDER, ADD MEDIA

$(document).on "click", ".modal-body-content a[class='ajax-add'], .media-modal-menu-new-folder", (event) ->

  event.preventDefault()
  modalId = $(event.target).parents(".mediaModalContainer").find('.fade').attr('id')
  
  folderName = $("#" + modalId + " .js-widget-title").text()

  displayLoader("#" + modalId + " .modal-body-content")

  $.ajax
    url: $(event.target).attr('data-url')
    method: 'GET'
    success: (response) ->
      new mediaFormView(
        html: response
        domContainer: $("#" + modalId + " .modal-body-content")
        title: $.trim(folderName)
      )
