OrchestraModalView = OrchestraView.extend(
  events:
    'click .close': 'close'

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
    $('.modal-footer', @$el).addClass("hidden-info")
    @options.domContainer.append @$el
    $("[data-prototype]", @$el).each ->
      PO.formPrototypes.addPrototype @

  close: ->
    $("#select2-drop-mask").click();
)
