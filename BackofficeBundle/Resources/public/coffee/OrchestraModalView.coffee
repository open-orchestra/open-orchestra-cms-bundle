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
      'entityType'
      'extendView'
    ])
    @loadTemplates [
        'OpenOrchestraBackofficeBundle:BackOffice:Underscore/orchestraModalView'
    ]
    return

  render: ->
    @setElement @renderTemplate('OpenOrchestraBackofficeBundle:BackOffice:Underscore/orchestraModalView', @options)
    if $('#dynamic-modal-title', @$el).length > 0
      @options.title = $('#dynamic-modal-title', @$el).html()
      $('.modal-title', @$el).html @options.title
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
