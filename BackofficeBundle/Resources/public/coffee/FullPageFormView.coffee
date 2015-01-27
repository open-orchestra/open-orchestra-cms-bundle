FullPageFormView = OrchestraView.extend(
  el: '#content'

  initialize: (options) ->
    @options = options
    @options.cid = @cid
    @element = options.element
    @title = options.title
    @listUrl = options.listUrl
    @completeOptions(@element) if @element != undefined
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
    @addColorPickerOnForm()
    $("[data-prototype]", @$el).each ->
      PO.formPrototypes.addPrototype $(this)
      return
    return

  addSelect2OnForm: ->
    if $(".select2", @$el).length > 0
      activateSelect2($(".select2", @$el))

  addColorPickerOnForm: ->
    if $(".colorpicker", @$el).length > 0
      activateColorPicker()

  addEventOnForm: ->
    options = @options
    $("form", @$el).on "submit", (e) ->
      e.preventDefault()
      $(this).ajaxSubmit
        success: (response) ->
          options.html = response
          view = new FullPageFormView(options)
      return

  changeLanguage: (event) ->
    event.preventDefault()
    language = $(event.currentTarget).data('language')
    link = @element.get('links')._self_without_parameters + '?language=' + language
    tableViewLoadSpecificElement(link, @title, @listUrl)

  changeVersion: (event) ->
    event.preventDefault()
    version = event.currentTarget.value
    link = @element.get('links')._self_without_parameters + '?language=' + @element.get('language') + '&version=' + version
    tableViewLoadSpecificElement(link, @title, @listUrl)

  redirectAfterStatusChange: ->
    link = @element.get('links')._self_without_parameters + '?language=' + @element.get('language') + '&version=' + @element.get('version')
    tableViewLoadSpecificElement(link, @title, @listUrl)
    return

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
