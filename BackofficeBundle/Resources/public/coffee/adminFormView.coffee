adminFormView = Backbone.View.extend(
  el: '#OrchestraBOModal'
  events:
    'keyup input#node_name' : 'refreshAlias'
  initialize: (options) ->
    @url = options.url
    @aliasIsRefreshable = false
    @method = if options.method then options.method else 'GET'
    _.bindAll this, "refreshAlias"
    @call()
    return
  call: ->
    viewContext = this
    displayLoader('.modal-body')
    $("#OrchestraBOModal").modal "show"
    $.ajax
      url: @url
      method: @method
      success: (response) ->
        viewContext.render(
          html: response
        )
      error: ->
        $('.modal-body', viewContext.el).html 'Erreur durant le chargement'
    return
  render: (options) ->
    @html = options.html
    $('.modal-body', @el).html @html
    $('.modal-title', @el).html $('#dynamic-modal-title').html()
    $("[data-prototype]").each ->
      PO.formPrototypes.addPrototype $(this)
      return
    @aliasIsRefreshable = $('input#node_alias').val() is ''
    @addEventOnForm()
  addEventOnForm: ->
    viewContext = this
    $("form", @$el).on "submit", (e) ->
      e.preventDefault() # prevent native submit
      $(this).ajaxSubmit
        statusCode:
          200: (response) ->
            view = viewContext.render(
              html: response
            )
            Backbone.history.loadUrl(Backbone.history.fragment)
    return
  refreshAlias: (event) ->
    if @aliasIsRefreshable
      $('input#node_alias').val(event.target.value.replace(/[^a-z0-9]/gi,'_'))
)
