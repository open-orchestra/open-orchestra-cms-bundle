adminFormView = Backbone.View.extend(
  el: '#OrchestraBOModal'
  initialize: (options) ->
    @html = options.html
    @render()
    return
  render: ->
    $('.modal-body', @$el).html @html
    $('.modal-title', @$el).html $('#dynamic-modal-title').html()
    $("[data-prototype]").each ->
      PO.formPrototypes.addPrototype $(this)
      return
    return
)