PageDeleteButtonView = OrchestraView.extend(

  initialize: (options) ->
    @options = @reduceOption(options, [
      'deleteUrl'
      'confirmText'
      'confirmTitle'
    ])
    @loadTemplates [
      "OpenOrchestraBackofficeBundle:BackOffice:Underscore/deleteButton"
    ]
    return

  render: ->
    @setElement @renderTemplate('OpenOrchestraBackofficeBundle:BackOffice:Underscore/deleteButton',
      deleteUrl: @options.deleteUrl
      confirmText: @options.confirmText
      confirmTitle: @options.confirmTitle
    )
    @$el.attr('data-widget-index', @options.widget_index)
    OpenOrchestra.RibbonButton.ribbonFormButtonView.setFocusedView(@, '.ribbon-form-button')
    return

)
