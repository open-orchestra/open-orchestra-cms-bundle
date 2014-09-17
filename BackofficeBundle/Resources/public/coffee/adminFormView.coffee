adminFormView = Backbone.View.extend(
  el: '#OrchestraBOModal'
  initialize: (options) ->
    @url = options.url
    @call()
    return
  call: ->
    current = this
    displayLoader('.modal-body')
    $("#OrchestraBOModal").modal "show"
    $.ajax
      url: @url
      method: 'GET'
      success: (response) ->
        current.render(
          html: response
        )
      error: ->
        $('.modal-body', el).html 'Erreur durant le chargement'
    return
  render: (options) ->
    @html = options.html
    el = @$el
    $('.modal-body', el).html @html
    $('.modal-title', el).html $('#dynamic-modal-title').html()
    $("[data-prototype]").each ->
      PO.formPrototypes.addPrototype $(this)
      return
    @addEventOnForm()
  addEventOnForm: ->
    current = this
    $("form", @$el).on "submit", (e) ->
      e.preventDefault() # prevent native submit
      $(this).ajaxSubmit
        success: (response) ->
          view = current.render(
            html: response
          )
      return
)
