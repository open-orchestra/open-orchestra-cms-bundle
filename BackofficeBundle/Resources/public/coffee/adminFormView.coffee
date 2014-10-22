vent = _.extend({}, Backbone.Events)
adminFormView = Backbone.View.extend(
  el: '#OrchestraBOModal'
  initialize: (options) ->
    @url = options.url
    @method = if options.method then options.method else 'GET'
    @events = {}
    if options.triggers
      for i of options.triggers
        @events[options.triggers[i].event] = options.triggers[i].name
        eval "this." + options.triggers[i].name + " = options.triggers[i].fct"
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
        if isLoginForm(response)
          redirectToLogin()
        else
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
            if $('#node_nodeId', viewContext.$el).length > 0
              nodeId = $('#node_nodeId', viewContext.$el).val()
              route = Backbone.history.fragment.split('/')
              route[2] = nodeId
              displayRoute = route.toString().replace(/,/g, '/')
            else
              displayRoute = Backbone.history.fragment
              Backbone.history.loadUrl(displayRoute)
            displayMenu(displayRoute)
    return
)
