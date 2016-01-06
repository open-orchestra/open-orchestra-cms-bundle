DataTableConfigurator = Backbone.Model.extend(
  setDataTableParameters: (dataTableParameters) ->
    @dataTableParameters = dataTableParameters
    @trigger 'dataTableParameters_loaded'
    return
  getDataTableParameters: (type) ->
    if @dataTableParameters[type]
      return @dataTableParameters[type]
    []
)

dataTableConfigurator = new DataTableConfigurator
