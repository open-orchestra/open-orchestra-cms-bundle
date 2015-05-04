mediaFormView = OrchestraView.extend(
  initialize: (options) ->
    @html = options.html
    @title = options.title
    @loadTemplates [
      'OpenOrchestraBackofficeBundle:BackOffice:Underscore/fullPageFormView'
    ]
    return

  render: ->
    $(@el).html @renderTemplate('OpenOrchestraBackofficeBundle:BackOffice:Underscore/fullPageFormView',
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
        success: (response) ->
          viewContext.html = response
          viewContext.render()
      return
)
