statusChannel = Backbone.Wreqr.radio.channel('status')

statusChannel.commands.setHandler 'ready', (view) ->
  $.ajax
    type: "GET"
    data:
      language: view.options.multiStatus.language
      version: view.options.multiStatus.version
    url: view.options.multiStatus.status_list
    success: (response) ->
      new StatusView(
        statuses: response.statuses
        currentStatus: view.options.multiStatus
      )
      return
