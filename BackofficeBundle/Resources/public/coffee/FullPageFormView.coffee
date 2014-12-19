FullPageFormView = OrchestraView.extend(
  el: '#content'

  initialize: (options) ->
    @options = options
    @element = options.element
    @options.currentLanguage = options.multiLanguage.language if options.multiLanguage
    @events = {}
    if options.triggers
      for i of options.triggers
        @events[options.triggers[i].event] = options.triggers[i].name
        eval "this." + options.triggers[i].name + " = options.triggers[i].fct"
    @loadTemplates [
      'fullPageFormView'
    ]
    if options.multiVersion
      @options.title = @renderTemplate('elementTitle',
        element: options.element
      )
    return

  render: ->
    $(@el).html(@renderTemplate('fullPageFormView', @options))
    $('.js-widget-title', @$el).html $('#generated-title', @$el).html()
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
    options = @options
    $("form", @$el).on "submit", (e) ->
      e.preventDefault()
      $(this).ajaxSubmit
        success: (response) ->
          options.html = response
          view = new FullPageFormView(options)
      return

#  renderWidgetStatus: ->
#    viewContext = this
#    $.ajax
#      type: "GET"
#      url: @element.get('links')._status_list
#      success: (response) ->
#        widgetStatus = viewContext.renderTemplate('widgetStatus',
#          current_status: viewContext.element.get('status')
#          statuses: response.statuses
#          status_change_link: viewContext.element.get('links')._self_status_change
#        )
#        addCustomJarvisWidget(widgetStatus)
#        return
)
