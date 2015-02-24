$(document).on 'click', "button.ajax-delete", (e) ->
  e.preventDefault()
  url = $(this).data("delete-url")
  confirm_text = $(this).data("confirm-text")
  confirm_title = $(this).data("confirm-title")
  redirectUrl = $(this).data('redirect-url')
  $("#OrchestraBOModal").modal "hide"
  smartConfirm(
    'fa-trash-o',
    confirm_title,
    confirm_text,
    callBackParams:
      url: url
    yesCallback: (params) ->
      $.ajax
        url: params.url
        method: 'DELETE'
        success: (response) ->
          if redirectUrl != undefined
            displayMenu(redirectUrl)
          else
            redirectUrl = appRouter.generateUrl 'showDashboard'
            displayMenu(redirectUrl)
          return
        error: (response) ->
          $('.modal-footer', this.el).html response.responseJSON.error.message
          return
    noCallback: ->
      $("#OrchestraBOModal").modal "show"
  )
