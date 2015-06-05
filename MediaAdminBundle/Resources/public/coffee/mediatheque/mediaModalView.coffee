MediaModalView = OrchestraView.extend(
  events:
    'click .mediaModalClose': 'closeModal'
  initialize: (options) ->
    @options = @reduceOption(options, [
      'body'
      'domContainer'
    ])
    @loadTemplates [
      "OpenOrchestraMediaAdminBundle:BackOffice:Underscore/mediaModalView"
    ]
    return
  render: (options) ->
    @setElement @renderTemplate('OpenOrchestraMediaAdminBundle:BackOffice:Underscore/mediaModalView', @options)
    @options.domContainer.html @$el
    @options.domContainer.modal "show"
    @options.domContainer.detach().appendTo('body')

  close: ->
    @options.domContainer.modal "hide"
    #@options.domContainer.detach().appendTo('body')
)
