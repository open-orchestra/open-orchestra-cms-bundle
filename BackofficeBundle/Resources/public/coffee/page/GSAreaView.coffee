GSAreaView = OrchestraView.extend(

  events:
    'click span.area-param': 'paramArea'
    'click span.area-remove': 'confirmRemoveArea'

  initialize: (options) ->
    @options = @reduceOption(options, [
      'area'
      'configuration'
      'published'
      'domContainer'
    ])
    @loadTemplates [
      "OpenOrchestraBackofficeBundle:BackOffice:Underscore/gsAreaView"
    ]
    return

  render: ->
    @setElement @renderTemplate('OpenOrchestraBackofficeBundle:BackOffice:Underscore/gsAreaView', @options)
    @options.domContainer.append @$el

  paramArea: (event) ->
    event.stopImmediatePropagation()
    label = "~no label yet~"
    label = @options.area.get("label")  if @options.area.get("label") isnt undefined
    adminFormViewClass = appConfigurationView.getConfiguration('area', 'showAdminForm')
    new adminFormViewClass(
      url: @options.area.get("links")._self_form
      title: "Area : " + label
      entityType: 'area'
    )
    return

  confirmRemoveArea: (event) ->
    smartConfirm(
      'fa-trash-o',
      @$el.data('delete-confirm-question'),
      @$el.data('delete-confirm-explanation'),
      callBackParams:
        areaView: @
      yesCallback: (params) ->
        grid = $('.grid-stack', params.areaView.domContainer).data('gridstack')
        grid.remove_widget(params.areaView.$el)
        grid.container.trigger('delete')
    )
)
