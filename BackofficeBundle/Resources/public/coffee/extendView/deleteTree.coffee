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
              refreshMenu(redirectUrl)
            else
              redirectUrl = appRouter.generateUrl 'showDashboard'
              refreshMenu(redirectUrl)
            return
      noCallback: ->
        $("#OrchestraBOModal").modal "show"
    )
}
