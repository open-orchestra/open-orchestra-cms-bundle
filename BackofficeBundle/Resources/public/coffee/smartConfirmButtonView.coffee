SmartConfirmButtonView = OrchestraView.extend(

  initialize: (options) ->
    @confirm = options.confirm
    return

  render: ->
    return @renderTemplate('smartConfirmButton',
      confirm: @confirm
    )
)
