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
    links = @options.element.get('links')
    panels = @getPanels links
    for panel in panels
      $("#superboxTab").append('<li id="' + panel.id + '"></li>')
      @callPanel panel

  getPanels: (links) ->
    panels = []
    if links._self_form
      panels[0] = {link:links._self_form, isActive:true, id:'form', title:'form'}
    for key in Object.keys(links)
      if infos = key.match(/^_self_panel_([0-9]+)_(.*)/)
        panels[infos[1]] = {link:links[key], isActive:false, id:infos[2], title:infos[2]}
    return panels

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

  callPanel: (panel) ->
    viewContext = @
    $.ajax
      url: panel["link"]
      method: "GET"
      success: (response) ->
        viewContext.addPanelTitle panel
        panel.response = response
        viewContext.addResponseInTab panel

  addResponseInTab: (element) ->
    $(".tab-content", @$el).prepend(
      @renderTemplate('OpenOrchestraBackofficeBundle:BackOffice:Underscore/elementPanelTab', element))

  addPanelTitle: (element) ->
    $("#" + element["id"]).replaceWith(
      @renderTemplate('OpenOrchestraBackofficeBundle:BackOffice:Underscore/elementPanelTitle', element))
)

