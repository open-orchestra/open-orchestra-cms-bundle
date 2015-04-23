FullPageFormView = OrchestraView.extend(
  el: '#content'

  initialize: (options) ->
    @options = options
    @options.listUrl = appRouter.generateUrl('listEntities', entityType: options.entityType) if options.listUrl == undefined
    @completeOptions(@options.element) if @options.element != undefined
    @events = {}
    @loadTemplates [
      'fullPageFormView',
      'elementTitle'
    ]
    return

  render: ->
    if @options.multiVersion
      @options.title = @renderTemplate('elementTitle',
        element: @options.element
      )
    $(@el).html(@renderTemplate('fullPageFormView', @options))
    $('.js-widget-title', @$el).html @options.title
    @addEventOnForm()
    Backbone.Wreqr.radio.commands.execute 'widget', 'loaded', @$el
    $("[data-prototype]", @$el).each ->
      PO.formPrototypes.addPrototype $(this)
      return
    return

  addEventOnForm: ->
    options = @options
    $("form", @$el).on "submit", (e) ->
      e.preventDefault()
      $(this).ajaxSubmit
        context:
          button: $(".submit_form",e.target).parent()
        success: (response) ->
          options.html = response
          view = new FullPageFormView(options)
        error: (response) ->
          options.html = response.responseText
          view = new FullPageFormView(options)
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
