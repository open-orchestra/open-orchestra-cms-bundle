$(document).on 'click', "button.ajax-delete", (e) ->
  e.preventDefault()
  url = $(this).data("delete-url")
  confirm_text = $(this).data("confirm-text")
  confirm_text = "Are you sure to delete this?"
  if confirm(confirm_text)
    $.ajax
      type: "DELETE"
      url: url
      success: (response) ->
        Backbone.history.history.back()
        displayMenu(Backbone.history.fragment)
        $("#OrchestraBOModal").modal "hide"
        return
      error: (response) ->
        $('.modal-footer', this.el).html response.responseJSON.error.message
        return
    return
  return
