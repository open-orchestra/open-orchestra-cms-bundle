FullPagePanelView = OrchestraView.extend(
  el: '#content'

  initialize: (options) ->
    @options = options
    @options.listUrl = appRouter.generateUrl('listEntities', entityType: options.entityType) if options.listUrl == undefined
    @completeOptions(@options.element) if @options.element != undefined
    @events = @events || {}
    @loadTemplates [
      'OpenOrchestraBackofficeBundle:BackOffice:Underscore/fullPagePanelView',
      'OpenOrchestraBackofficeBundle:BackOffice:Underscore/elementTitle',
      'OpenOrchestraBackofficeBundle:BackOffice:Underscore/elementPanelTitle',
      'OpenOrchestraBackofficeBundle:BackOffice:Underscore/elementPanelTab'
    ]
    return

  render: ->
    if @options.multiVersion
      @options.title = @renderTemplate('OpenOrchestraBackofficeBundle:BackOffice:Underscore/elementTitle',
        element: @options.element
      )
    $(@el).html(@renderTemplate('OpenOrchestraBackofficeBundle:BackOffice:Underscore/fullPagePanelView', @options))
    $('.js-widget-title', @$el).html @options.title
    links = @options.element.get('links')
    panels = @getPanels links
    for panel in panels
      $("#superboxTab").append('<li id="' + panel["id"] + '"></li>')
      @callPanel panel["link"], panel["isActive"], panel["id"], panel["title"]


  getPanels: (links) ->
    panels = []
    for key in Object.keys(links)
      if /^_self_form$/.test(key) or /^_self_panel_/.test(key)
        if /^_self_form$/.test(key)
          id = "form"
          active = true
          position = 0
        else
          id = key.replace(/^_self_panel_[0-9]+_/,"")
          active = false
          position = key.replace(/^_self_panel_([0-9]+)_.+$/,'$1')
        panels[position] = []
        panels[position]["link"] = links[key]
        panels[position]["isActive"] = active
        panels[position]["id"] = id
        panels[position]["title"] = id
    return panels

  addEventOnPanelForm: (id) ->
    options = @options
    viewContext = @
    $("form", @$("#tab-" + id)).on "submit", (e) ->
      e.preventDefault()
      $(this).ajaxSubmit
        context:
          button: $(".submit_form",e.target).parent()
        success: (response) ->
          container = $(".tab-pane.active")
          container.html(response)
          container.attr("id").replace("tab-","")
          viewContext.addEventOnPanelForm  container.attr("id").replace("tab-","")
        error: (response) ->
          container = $(".tab-pane.active")
          container.html(response.responseText)
          viewContext.addEventOnPanelForm  container.attr("id").replace("tab-","")
      return

  callPanel: (url, isActive, id, title) ->
    viewContext = @
    $.ajax
      url: url
      method: "GET"
      success: (response) ->
        element = []
        element["isActive"] = isActive
        element["id"] = id
        element["title"] = title
        viewContext.addPanelTitle(element);
        element["response"] = response
        viewContext.addResponseInTab(element)
        viewContext.addEventOnPanelForm id

  addResponseInTab: (element) ->
    $(".tab-content").prepend(
      @renderTemplate('OpenOrchestraBackofficeBundle:BackOffice:Underscore/elementPanelTab', element))

  addPanelTitle: (element) ->
    $("#" + element["id"]).replaceWith(
      @renderTemplate('OpenOrchestraBackofficeBundle:BackOffice:Underscore/elementPanelTitle', element))

  completeOptions: (element) ->
    @options = $.extend(@options, multiLanguage:
      language_list : element.get('links')._language_list
      language : element.get('language')
      path: 'showEntityWithLanguage'
    ) if element.get('links')._language_list

    @options = $.extend(@options, multiStatus:
      language: element.get('language')
      version: element.get('version')
      status_list: element.get('links')._status_list
      status: element.get('status')
      self_status_change: element.get('links')._self_status_change
    ) if element.get('links')._status_list

    @options = $.extend(@options, multiVersion:
      language: element.get('language')
      version: element.get('version')
      self_version: element.get('links')._self_version
      path: 'showEntityWithLanguageAndVersion'
    ) if element.get('links')._self_version

    @options = $.extend(@options, duplicate:
      language: element.get('language')
      self_duplicate: element.get('links')._self_duplicate
      path: 'showEntityWithLanguage'
    ) if element.get('links')._self_duplicate
)
