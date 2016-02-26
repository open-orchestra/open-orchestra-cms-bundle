DataTableConfigurator = Backbone.Model.extend(
  setDataTableParameters: (dataTableParameters) ->
    @dataTableParameters = dataTableParameters
    @trigger 'dataTableParameters_loaded'
    return
  getDataTableParameters: (type) ->
    if @dataTableParameters.entity_parameter and @dataTableParameters.entity_parameter[type]
      return @dataTableParameters.entity_parameter[type]
    []
)

dataTableConfigurator = new DataTableConfigurator
