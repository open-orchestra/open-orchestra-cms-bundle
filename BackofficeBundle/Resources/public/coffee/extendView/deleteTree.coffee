extendView = extendView || {}
extendView['deleteTree'] = {
  events:
    'click .ajax-delete': 'deleteElement'

  deleteElement: (event) ->
    event.preventDefault()
    target = $(event.currentTarget)
    url = target.data("delete-url")
    confirm_text = target.data("confirm-text")
    confirm_title = target.data("confirm-title")
    redirectUrl = target.data('redirect-url')
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
            target.parents('.modal-footer').html response.responseJSON.error.message
            return
      noCallback: ->
        $("#OrchestraBOModal").modal "show"
    )
}
