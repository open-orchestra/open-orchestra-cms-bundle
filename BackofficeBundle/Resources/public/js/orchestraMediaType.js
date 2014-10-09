//--[ OPEN MEDIA MODAL ]--//
$(document).on("click", ".mediaModalOpen", function(event) {
    var button = event.currentTarget;
    var modalId = $(button).data("target");
    var mediaModal = $("#" + modalId);
    mediaModal.css("top", "-100px");
    
    mediaModal.modal('show');
});

//--[ CLOSE MEDIA MODAL ]--//
$(document).on("click", ".mediaModalClose", function(event) {
    var button = event.currentTarget;
    var modalId = $(button).data("target");
    var mediaModal = $("#" + modalId);
    mediaModal.modal('hide');
    mediaModal.css("top", "250px");
});