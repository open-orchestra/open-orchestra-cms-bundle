extendView = extendView || {}
extendView['deleteTree'] = {
  events:
    'click .ajax-delete': 'deleteElement'

  deleteElement: (event) ->
    event.preventDefault()
    url = $(event.currentTarget).data("delete-url")
    confirm_text = $(event.currentTarget).data("confirm-text")
    confirm_title = $(event.currentTarget).data("confirm-title")
    redirectUrl = $(event.currentTarget).data('redirect-url')
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
            $('.modal-footer', @el).html response.responseJSON.error.message
            return
      noCallback: ->
        $("#OrchestraBOModal").modal "show"
    )
}
