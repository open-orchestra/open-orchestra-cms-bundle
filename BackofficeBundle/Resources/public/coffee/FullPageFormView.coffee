FullPageFormView = Backbone.View.extend(
  el: '#content'
  initialize: (options) ->
    @html = options.html
    @title = options.title
    @listUrl = options.listUrl
    @formTemplate = _.template($('#fullPageFormView').html())
    @render()
    return
  render: ->
    $(".widget-body", @el).replaceWith $("<div/>").html(@formTemplate(
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
