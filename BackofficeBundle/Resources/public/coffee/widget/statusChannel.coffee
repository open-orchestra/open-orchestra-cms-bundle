widgetChannel.bind 'ready', (view) ->
  if view.options && view.options.multiStatus
    $.ajax
      type: "GET"
      data:
        language: view.options.multiStatus.language
        version: view.options.multiStatus.version
      url: view.options.multiStatus.status_list
      success: (response) ->
        viewClass = appConfigurationView.getConfiguration(view.options.entityType, 'showStatus')
        new viewClass(
          statuses: response.statuses
          currentStatus: view.options.multiStatus
        )
        return
