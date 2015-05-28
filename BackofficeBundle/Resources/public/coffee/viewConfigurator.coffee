OrchestraViewConfigurator = ->
  configurations: {}
  baseConfigurations:
    'edit': 'FullPageFormView'
    'add': 'FullPageFormView'

  addConfiguration: (entityType, action, view) ->
    @configurations[entityType][action] = view
    return

  getConfiguration: (entityType, action) ->
    entityTypeConfiguration = @configurations[entityType]
    if typeof entityTypeConfiguration != 'undefined'
      view = entityTypeConfiguration[action]
      if typeof view != 'undefined'
        return view
    return @baseConfigurations[action]


appConfigurationView = new OrchestraViewConfigurator()