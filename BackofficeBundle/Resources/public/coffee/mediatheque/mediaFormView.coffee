mediaFormView = Backbone.View.extend(
  initialize: (options) ->
    @html = options.html
    @title = 'Add a media'
    @formTemplate = _.template($('#fullPageFormView').html())
    @render()
    return
  render: ->
    $(@el).html @formTemplate (
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
