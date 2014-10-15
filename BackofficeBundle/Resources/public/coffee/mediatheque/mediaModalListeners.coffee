#--[ OPEN MEDIA MODAL ]--#

$(document).on "click", ".mediaModalOpen", (event) ->
  modalId = $(event.currentTarget).data("target")
  mediaModal = $("#" + modalId)
  orchestraModal = $("#OrchestraBOModal")
  
  mediaModal.parent().detach().appendTo('body')
  $('#' + modalId + ' .modal-body-menu').empty()
  $('#' + modalId + ' .modal-body-content').empty()
  
  mediaModal.css "width", orchestraModal.css("width")
  mediaModal.css "height", orchestraModal.css("height")
  
  mediaModal.modal "show"
  
  view = new mediaModalView(
    menuUrl: $('#' + modalId + ' .modal-body-menu').data('url'),
    el: '#' + modalId
  )
  
  return


#--[ CLOSE MEDIA MODAL ]--#

$(document).on "click", ".mediaModalClose", (event) ->
  mediaModal = $("#" + $(event.currentTarget).data("target"))
  mediaModal.modal "hide"
  mediaModalContainer = mediaModal.parent()
  
  mediaModalContainer.detach().appendTo($('#' + mediaModalContainer.data('input')).parent())
  
  mediaModal.css "width", "0px"
  mediaModal.css "height", "0px"
  
  return


#--[ FOLDER CLICKED ]--#

$(document).on "click", ".media-modal-menu-folder", (event) ->
  modalId = $(event.target).parents(".mediaModalContainer").find(".fade").attr("id")
  displayLoader "#" + modalId + " .modal-body-content"
  tableViewLoad $(event.target), "#" + modalId + " .modal-body-content",
    select: true


#--[ ADD MEDIA ]--#

$(document).on "click", ".modal-body-content a[class^='ajax-add-']", (event) ->
  modalId = $(event.target).parents(".mediaModalContainer").find('.fade').attr('id')
  
  folderName = $("#" + modalId + " .js-widget-title").text()
  
  displayLoader("#" + modalId + " .modal-body-content")
  
  $.ajax
    url: $(event.target).attr('href')
    method: 'GET'
    success: (response) ->
      view = new mediaFormView(
        html: response
        el: ("#" + modalId + " .modal-body-content")
        title: $.trim(folderName)
      )
