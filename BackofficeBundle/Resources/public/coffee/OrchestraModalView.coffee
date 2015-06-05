OrchestraModalView = OrchestraView.extend(
  events:
    'click .close': 'close'
    'resize .modal-dialog': 'resize'

  initialize: (options) ->
    @options = @reduceOption(options, [
      'title'
      'body'
      'footer'
      'domContainer'
    ])
    @loadTemplates [
        'OpenOrchestraBackofficeBundle:BackOffice:Underscore/orchestraModalView'
    ]
    return

  render: ->
    @setElement @renderTemplate('OpenOrchestraBackofficeBundle:BackOffice:Underscore/orchestraModalView', @options)
    @options.domContainer.html @$el
    $("[data-prototype]", @options.domContainer.$el).each ->
      PO.formPrototypes.addPrototype $(@)
    $("#OrchestraBOModal").modal "show"

  close: ->
    $("#select2-drop-mask").click();

  resize: (event) ->
    target = $(event.currentTarget)
    target.prev().height(target.parent().height())
)
