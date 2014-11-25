vent = _.extend({}, Backbone.Events)

adminFormView = OrchestraView.extend(
  el: '#OrchestraBOModal'

  initialize: (options) ->
    @url = options.url
    @method = if options.method then options.method else 'GET'
    @deleteurl = options.deleteurl if options.deleteurl
    @confirmtext = options.confirmtext if options.confirmtext
    @events = {}
    if options.triggers
      for i of options.triggers
        @events[options.triggers[i].event] = options.triggers[i].name
        eval "this." + options.triggers[i].name + " = options.triggers[i].fct"
    @loadTemplates [
        'deleteButton'
    ]
    return

  render: ->
    viewContext = this
    displayLoader('.modal-body')
    $("#OrchestraBOModal").modal "show"
    $('.modal-footer', @el).html @renderTemplate('deleteButton')
    $.ajax
      url: @url
      method: @method
      success: (response) ->
        if isLoginForm(response)
          redirectToLogin()
        else
          viewContext.renderContent(
            html: response
          )
      error: ->
        $('.modal-body', viewContext.el).html 'Erreur durant le chargement'
    return

  renderContent: (options) ->
    @html = options.html
    $('.modal-body', @el).html @html
    $('.modal-title', @el).html $('#dynamic-modal-title').html()
    if @deleteurl != undefined && @confirmtext != undefined
      $('.ajax-delete', @el).attr('data-delete-url', @deleteurl)
      $('.ajax-delete', @el).attr('data-confirm-text', @confirmtext)
      $('.modal-footer', @el).show()
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
            view = viewContext.renderContent(
              html: response
            )
            if $('#node_nodeId', viewContext.$el).length > 0
              displayRoute = appRouter.generateUrl "showNode",
                nodeId: $('#node_nodeId', viewContext.$el).val()
            else
              displayRoute = Backbone.history.fragment
              Backbone.history.loadUrl(displayRoute)
            displayMenu(displayRoute)
          400: (response) ->
            view = viewContext.renderContent(
              html: response.responseText
            )
    return
)
