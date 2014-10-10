//--[ OPEN MEDIA MODAL ]--//
$(document).on("click", ".mediaModalOpen", function(event) {
    var button = event.currentTarget;
    var modalId = $(button).data("target");
    var mediaModal = $("#" + modalId);

    var orchestraModal = $("#OrchestraBOModal");

    var mediaModalDialog = mediaModal.find(".modal-dialog:first");
    var left = (mediaModalDialog.width() - orchestraModal.width() - $.scrollbarWidth()) / 2;

    var mediaModalBody = mediaModal.find(".modal-body:first");

    mediaModal.css("top", "-88px");
    mediaModal.css("left", left + "px");
    mediaModal.css("width", orchestraModal.css('width'));
    mediaModal.css("height", orchestraModal.css('height'));
    mediaModalBody.css("min-height", (mediaModal.height() - 120) + "px");

    mediaModal.modal('show');
});

//--[ CLOSE MEDIA MODAL ]--//
$(document).on("click", ".mediaModalClose", function(event) {
    var button = event.currentTarget;
    var modalId = $(button).data("target");
    var mediaModal = $("#" + modalId);
    mediaModal.modal('hide');
    mediaModal.css("width", "0px");
    mediaModal.css("height", "0px");
});