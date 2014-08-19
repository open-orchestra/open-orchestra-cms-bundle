adminFormView = Backbone.View.extend(
  el: '#myModal'
  initialize: (options) ->
    @html = options.html
    @render()
    return
  render: ->
    $('.modal-body', @$el).html @html
    $("[data-prototype]").each ->
      PO.formPrototypes.addPrototype $(this)
      return
    return
)