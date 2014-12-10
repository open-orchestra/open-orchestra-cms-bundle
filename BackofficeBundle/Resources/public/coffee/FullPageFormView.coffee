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
    $(@el).replaceWith $("<div/>").html(@renderTemplate('fullPageFormView',
      html: @html
      listUrl: @listUrl
    ))
    $('.js-widget-title', @$el).text @title
    $("[data-prototype]").each ->
      PO.formPrototypes.addPrototype $(this)
      return
    @addEventOnForm()
    @addSelect2OnForm()
    return

  addSelect2OnForm: ->
    if $(".select2", @$el).length > 0
      tags = $(".select2", @$el).data('tags')
      $(".select2", @$el).select2(
        tags: tags
        createSearchChoice: (term, data) ->
          if $(data).filter(->
            @text.localeCompare(term) is 0
          ).length is 0
            id: term
            text: term
            isNew: true
        formatResult: (term) ->
          if term.isNew
            "<span class=\"label label-danger\">New</span> " + term.text
          else
            term.text
      )

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
