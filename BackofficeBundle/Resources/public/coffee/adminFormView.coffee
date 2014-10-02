adminFormView = Backbone.View.extend(
  el: '#OrchestraBOModal'
  initialize: (options) ->
    @url = options.url
    @method = if options.method then options.method else 'GET'
    @call()
    return
  call: ->
    current = this
    displayLoader('.modal-body')
    $("#OrchestraBOModal").modal "show"
    $.ajax
      url: @url
      method: @method
      success: (response) ->
        current.render(
          html: response
        )
      error: ->
        $('.modal-body', current.el).html 'Erreur durant le chargement'
    return
  render: (options) ->
    @html = options.html
    $('.modal-body', @el).html @html
    $('.modal-title', @el).html $('#dynamic-modal-title').html()
    $("[data-prototype]").each ->
      PO.formPrototypes.addPrototype $(this)
      return
    @addEventOnForm()
  addEventOnForm: ->
    current = this
    $("form", @$el).on "submit", (e) ->
      e.preventDefault() # prevent native submit
      $(this).ajaxSubmit
        statusCode:
          200: (response) ->
            view = current.render(
              html: response
            )
            Backbone.history.loadUrl(Backbone.history.fragment)
    return
)
