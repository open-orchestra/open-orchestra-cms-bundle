mediaFormView = OrchestraView.extend(
  initialize: (options) ->
    @html = options.html
    @title = options.title
    @loadTemplates [
      'fullPageFormView'
    ]
    return

  render: ->
    $(@el).html @renderTemplate('fullPageFormView',
      html: @html
      listUrl: @listUrl
    )
    $('.js-widget-title', @$el).text @title
    $('.back-to-list', @el).remove()
    $("[data-prototype]").each ->
      PO.formPrototypes.addPrototype $(this)
      return
    @addEventOnForm()
    return

  addEventOnForm: ->
    viewContext = this
    $("form", @$el).on "submit", (e) ->
      e.preventDefault() # prevent native submit
      $(this).ajaxSubmit
        success: (response) ->
          viewContext.html = response
          viewContext.render()
      return
)
