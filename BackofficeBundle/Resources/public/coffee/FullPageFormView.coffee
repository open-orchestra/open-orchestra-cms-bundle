FullPageFormView = Backbone.View.extend(
  el: '#content'
  events:
    'click a.ajax-back-to-list': 'clickBackToList'
  initialize: (options) ->
    @html = options.html
    @title = options.title
    @listUrl = options.listUrl
    @displayedElements = options.displayedElements
    @formTemplate = _.template($('#fullPageFormView').html())
    @render()
    return
  render: ->
    $(@el).html @formTemplate (
      html: @html
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
    $("form", @$el).on "submit", (e) ->
      e.preventDefault() # prevent native submit
      $(this).ajaxSubmit
        success: (response) ->
          view = new FullPageFormView(
            html: response
            title: title
            listUrl: listUrl
          )
      return
  clickBackToList: (event) ->
    event.preventDefault()
    displayedElements = @displayedElements
    title = @title
    listUrl = @listUrl
    $.ajax
      url: listUrl
      method: 'GET'
      success: (response) ->
        elements = new TableviewElement
        elements.set response
        view = new TableviewCollectionView(
          elements: elements
          displayedElements: displayedElements
          title: title
          listUrl: listUrl
        )
)