adminFormView = Backbone.View.extend(
  el: '#OrchestraBOModal'
  initialize: (options) ->
    @url = options.url
    @render()
    return
  render: ->
    el = @$el
    displayLoader('.modal-body')
    $("#OrchestraBOModal").modal "show"
    $.ajax
      url: @url
      method: 'GET'
      success: (response) ->
        html = response
        $('.modal-body', el).html html
        $('.modal-title', el).html $('#dynamic-modal-title').html()
        $("[data-prototype]").each ->
          PO.formPrototypes.addPrototype $(this)
          return
      error: ->
        $('.modal-body', el).html 'Erreur durant le chargement'
    @addEventOnForm()
    return
  addEventOnForm: ->
    $("form", @$el).on "submit", (e) ->
      e.preventDefault() # prevent native submit
      $(this).ajaxSubmit
        success: (response) ->
          view = new adminFormView(
            html: response
          )
      return
)

