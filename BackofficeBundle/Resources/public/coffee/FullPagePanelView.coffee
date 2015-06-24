FullPagePanelView = FullPageFormView.extend(

  initialize: (options) ->
    @initializer options
    @loadTemplates [
      'OpenOrchestraBackofficeBundle:BackOffice:Underscore/fullPagePanelView',
      'OpenOrchestraBackofficeBundle:BackOffice:Underscore/elementTitle',
      'OpenOrchestraBackofficeBundle:BackOffice:Underscore/elementPanelTitle',
      'OpenOrchestraBackofficeBundle:BackOffice:Underscore/elementPanelTab'
    ]
    return

  render: ->
    @setElement @renderTemplate('OpenOrchestraBackofficeBundle:BackOffice:Underscore/fullPagePanelView', @options)
    @options.domContainer.html @$el
    $('.js-widget-title', @options.domContainer).html @options.title
    @options.links = @options.element.get('links')
    @getPanels()
    @callPanels()

  getPanels: ->
    @options.panels = []
    if @options.links._self_form
      @options.panels.push {link:@options.links._self_form, isActive:true, id:'form'}
    for key in Object.keys(@options.links)
      if infos = key.match(/^_self_panel_(.*)/)
        @options.panels.push {link:@options.links[key], isActive:false, id:infos[1]}

  addEventOnForm: (event)->
    event.preventDefault()
    target = $(event.target)
    target.ajaxSubmit
      context:
        button: $(".submit_form", target).parent()
      success: (response) ->
        target.parent().html response
      error: (response) ->
        target.parent().html response.responseText
    return

  callPanels: ->
    viewContext = @
    for panel in @options.panels
      $("#superboxTab", viewContext.$el).append('<li id="' + panel.id + '"></li>')
      do (panel) ->
        $.ajax
          url: panel["link"]
          method: "GET"
          success: (response) ->
            panel.response = response
            viewContext.addResponseInTab panel
            panel.title = $('#tab-' + panel.id + ' form', viewContext.$el).data('title')
            viewContext.addPanelTitle panel

  addResponseInTab: (element) ->
    $(".tab-content", @$el).prepend(
      @renderTemplate('OpenOrchestraBackofficeBundle:BackOffice:Underscore/elementPanelTab', element))

  addPanelTitle: (element) ->
    $("#" + element["id"]).replaceWith(
      @renderTemplate('OpenOrchestraBackofficeBundle:BackOffice:Underscore/elementPanelTitle', element))
)

