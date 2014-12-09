FullPageFormView = OrchestraView.extend(
  el: '#content'

  initialize: (options) ->
    @html = options.html
    @title = options.title
    @listUrl = options.listUrl
    if(options.multiLanguage)
      @multiLanguage = options.multiLanguage
    if(options.multiStatus)
      @multiStatus = options.multiStatus
    @options = options
    @events = {}
    if options.triggers
      for i of options.triggers
        @events[options.triggers[i].event] = options.triggers[i].name
        eval "this." + options.triggers[i].name + " = options.triggers[i].fct"
    @loadTemplates [
      'fullPageFormView'
    ]
    @element = options.element
    if @element
      @loadTemplates [
        "widgetStatus"
      ]
    return

  render: ->
    $(@el).html(@renderTemplate('fullPageFormView',
      html: @html
      listUrl: @listUrl
      element: @element if @element
    ))
    $('.js-widget-title', @$el).text @title
    if @element && @element.get('links')._self_status
      @renderWidgetStatus()
      if @element.status_label == 'published'
        $("#orchestra_content_submit").addClass('disabled')

    @addEventOnForm()
    @addSelect2OnForm()
    $("[data-prototype]").each ->
      PO.formPrototypes.addPrototype $(this)
      return
    return

  addSelect2OnForm: ->
    if $(".select2", @$el).length > 0
      activateSelect2($(".select2", @$el))

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

  renderWidgetStatus: ->
    viewContext = this
    $.ajax
      type: "GET"
      url: @element.get('links')._status_list
      success: (response) ->
        widgetStatus = viewContext.renderTemplate('widgetStatus',
          current_status: viewContext.element.status
          statuses: response.statuses
          status_change_link: viewContext.element.links._self_status_change
        )
        addCustomJarvisWidget(widgetStatus)
        return
)
