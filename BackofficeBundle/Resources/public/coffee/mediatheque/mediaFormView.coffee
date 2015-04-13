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
    $("[data-prototype]", @$el).each ->
      PO.formPrototypes.addPrototype $(this)
      return
    @addEventOnForm()
    return

  addEventOnForm: ->
    viewContext = this
    $("form", @$el).on "submit", (e) ->
      e.preventDefault() # prevent native submit
      $(this).ajaxSubmit
        context:
          button: $(".submit_form",e.target).parent()
          isSave: true
        success: (response) ->
          viewContext.html = response
          viewContext.render()
      return
)
