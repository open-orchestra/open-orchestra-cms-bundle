mediaFormView = Backbone.View.extend(
  initialize: (options) ->
    @html = options.html
    @title = 'Add a media'
#    @listUrl = options.listUrl
    @listUrl = 'options.listUrl'
    @formTemplate = _.template($('#fullPageFormView').html())
    @render()
    return
  render: ->
    $(@el).html @formTemplate (
      html: @html
      listUrl: @listUrl
    )
    $('.js-widget-title', @$el).text @title
    $("[data-prototype]").each ->
      PO.formPrototypes.addPrototype $(this)
      return
    @addEventOnForm()
    return
  addEventOnForm: ->
    title = @title
    listUrl = @listUrl
#    displayedElements = @displayedElements
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
