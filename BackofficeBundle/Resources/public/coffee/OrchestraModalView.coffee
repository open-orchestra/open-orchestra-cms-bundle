OrchestraModalView = OrchestraView.extend(
  events:
    'click .close': 'close'
    'resize .modal-dialog': 'resize'

  initialize: (options) ->
    @options = @reduceOption(options, [
      'title'
      'html'
      'actionButtons'
      'domContainer'
      'entityType'
      'extendView'
      'formView'
      'submitted'
    ])
    @loadTemplates [
        'OpenOrchestraBackofficeBundle:BackOffice:Underscore/orchestraModalView'
    ]
    return

  render: ->
    @setElement @renderTemplate('OpenOrchestraBackofficeBundle:BackOffice:Underscore/orchestraModalView', @options)
    if (title = $('form', @$el).data('title'))
      @options.title = title
      $('.modal-title', @$el).html @options.title
    @options.domContainer.html @$el
    $("#OrchestraBOModal").modal "show"

  close: ->
    $("#select2-drop-mask").click();

  resize: (event) ->
    target = $(event.currentTarget)
    target.prev().height(target.parent().height())
)
