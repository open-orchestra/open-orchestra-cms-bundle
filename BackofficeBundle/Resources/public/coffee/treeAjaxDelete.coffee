$(document).on 'click', "button.ajax-delete", (e) ->
  e.preventDefault()
  url = $(this).data("delete-url")
  confirm_text = $(this).data("confirm-text")
  redirectUrl = $(this).data('redirect-url')
  $("#OrchestraBOModal").modal "hide"
  smartConfirm(
    title: new SmartConfirmTitleView(
      titleWhite: 'Delete'
      titleColorized: 'this element'
      logo: 'fa-sign-out'
    )
    text: confirm_text
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
