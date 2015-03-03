adminFormView = OrchestraView.extend(
  el: '#OrchestraBOModal'

  initialize: (options) ->
    @url = options.url
    @options = options
    @deleteButton = @options.deleteurl && @options.confirmtext && @options.confirmtitle
    @method = if options.method then options.method else 'GET'
    @events = {}
    if options.triggers
      for i of options.triggers
        @events[options.triggers[i].event] = options.triggers[i].name
        eval "this." + options.triggers[i].name + " = options.triggers[i].fct"
    @formEvent = 'submit'
    @formClass = 'form'
    @loadTemplates [
        'deleteButton'
    ]
    $('.modal-footer', @el).addClass("hidden-info")
    return

  render: ->
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
          viewContext.renderContent(
            html: response
          )
      error: ->
        $('.modal-body', viewContext.el).html 'Erreur durant le chargement'
    return

  renderContent: (options) ->
    $('.modal-body', @el).html options.html
    $('.modal-title', @el).html $('#dynamic-modal-title').html()
    if @deleteButton && $('form.form-disabled', @el).length == 0
      $('.modal-footer', @el).html @renderTemplate('deleteButton', @options)
      $('.modal-footer', @el).removeClass("hidden-info")
      $('.modal-footer', @el).prepend($('.submit_form', @$el))
      @formEvent = 'click'
      @formClass = '.submit_form'
    $("[data-prototype]", @$el).each ->
      PO.formPrototypes.addPrototype $(this)
      return
    @addEventOnSave()
    Backbone.Wreqr.radio.commands.execute 'widget', 'loaded', @$el

  addEventOnSave: ->
    viewContext = this
    $(@formClass, @$el).on @formEvent, (e) ->
      e.preventDefault() # prevent native submit
      displayLoader($('.submit_form', viewContext.$el).parent())
      $("form", viewContext.$el).ajaxSubmit
        statusCode:
          200: (response) ->
            view = viewContext.renderContent(
              html: response
            )
            if $('#node_nodeId', viewContext.$el).length > 0
              displayRoute = appRouter.generateUrl "showNode",
                nodeId: $('#node_nodeId', viewContext.$el).val()
            else if $('#template_templateId', viewContext.$el).length > 0
              displayRoute = appRouter.generateUrl "showTemplate",
                templateId: $('#template_templateId', viewContext.$el).val()
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
