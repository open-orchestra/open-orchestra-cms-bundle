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
    viewContext = @
    if (title = $('form', @$el).data('title'))
      @options.title = title
      $('.modal-title', @$el).html @options.title
    @options.domContainer.html @$el
    @options.domContainer.modal "show"
    @options.domContainer.on 'hidden.bs.modal', ->
      deactivateForm(viewContext, $('form', viewContext.$el))
      return

  close: ->
    $("#select2-drop-mask").click();

  resize: (event) ->
    target = $(event.currentTarget)
    target.prev().height(target.parent().height())
)
