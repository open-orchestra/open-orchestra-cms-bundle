FullPageFormView = OrchestraView.extend(
  el: '#content'

  initialize: (options) ->
    @html = options.html
    @title = options.title
    @listUrl = options.listUrl
    @events = {}
    if options.triggers
      for i of options.triggers
        @events[options.triggers[i].event] = options.triggers[i].name
        eval "this." + options.triggers[i].name + " = options.triggers[i].fct"
    @loadTemplates [
      'fullPageFormView'
    ]
    return

  render: ->
    $(".widget-body", @el).replaceWith $("<div/>").html(@renderTemplate('fullPageFormView',
      html: @html
      listUrl: @listUrl
    )).find(".widget-body")
    $('.js-widget-title', @$el).text @title
    $("[data-prototype]").each ->
      PO.formPrototypes.addPrototype $(this)
      return
    @addEventOnForm()
    return

  addEventOnForm: ->
    title = @title
    listUrl = @listUrl
    displayedElements = @displayedElements
    $("form", @$el).on "submit", (e) ->
      e.preventDefault() # prevent native submit
      $(this).ajaxSubmit
        success: (response) ->
          view = new FullPageFormView(
            html: response
            title: title
            listUrl: listUrl
            displayedElements: displayedElements
          )
      return
)
