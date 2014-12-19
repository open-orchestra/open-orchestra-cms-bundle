$(document).on 'click', "button.ajax-delete", (e) ->
  e.preventDefault()
  url = $(this).data("delete-url")
  confirm_text = $(this).data("confirm-text")
  redirectUrl = $(this).data('redirect-url')
  $("#OrchestraBOModal").modal "hide"
  smartConfirm(
    'fa-sign-out',
    'Delete this element',
    confirm_text,
    yesCallbackParams:
      url: url
    yesCallback: (params) ->
      $.ajax
        url: params.url
        method: 'DELETE'
        success: (response) ->
          if redirectUrl != undefined
            displayMenu(redirectUrl)
          else
            redirectUrl = appRouter.generateUrl 'showHome'
            Backbone.history.navigate(redirectUrl, {trigger:true})
            displayMenu(redirectUrl)
          return
        error: (response) ->
          $('.modal-footer', this.el).html response.responseJSON.error.message
          return
    noCallback: ->
      $("#OrchestraBOModal").modal "show"
  )
