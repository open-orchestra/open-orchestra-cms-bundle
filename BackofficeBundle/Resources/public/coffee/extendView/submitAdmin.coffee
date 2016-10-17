extendView = extendView || {}
extendView['submitAdmin'] = {
  events:
    'click .submit_form': 'addEventOnSave'

  openForm: (response, form) ->
    viewClass = appConfigurationView.getConfiguration(@options.entityType, @options.formView)
    window.OpenOrchestra.FormBehavior.channel.trigger 'deactivate', @, form

    new viewClass(@addOption(
      html: response
      submitted: true
    ))
    
    if $('.tab-content .submit_form.btn-in-ribbon',@.$el).length > 0
      $('.tab-content .submit_form.btn-in-ribbon',@.$el).removeClass('btn-in-ribbon')
      OpenOrchestra.RibbonButton.ribbonFormButtonView.setFocusedView @, '.ribbon-form-button'
    $(document).scrollTop 0

  httpOk: (response, form) ->
    @openForm(response[0], form)
    
  httpBadRequest: (response, form) ->
    widgetChannel.trigger 'form-error', @
    @openForm(response.responseText, form)
  
  httpCreated: (response) ->
    widgetChannel.trigger 'element-created', @
    displayRoute = $("#nav-" + @options.entityType).attr('href')
    Backbone.history.navigate(displayRoute, {trigger: true})
    viewClass = appConfigurationView.getConfiguration(@options.entityType, 'showFlashBag')
    new viewClass(@addOption(
      html: response[0]
      domContainer: $('h1.page-title').parent()
    ))
    $(document).scrollTop 0

  httpForbidden: (response, form) ->
    displayRoute = OpenOrchestra.ForbiddenAccessRedirection[Backbone.history.fragment]
    if typeof displayRoute != 'undefined'
      window.OpenOrchestra.FormBehavior.channel.trigger 'deactivate', @, form
    if (displayRoute == Backbone.history.fragment)
      Backbone.history.loadUrl(displayRoute)
    else
      Backbone.history.navigate(displayRoute, {trigger: true})
  
  addEventOnSave: (event) ->
    viewContext = @
    form = if (clone = $(event.target).data('clone')) then $('#' + clone).closest('form') else $(event.target).closest('form')
    if $("textarea.tinymce", form).length > 0
      tinymce.triggerSave()
    if form[0].checkValidity()
      event.preventDefault()
      $.ajax
        type: 'POST'
        url: form.data('action')
        data: form.serialize()
        context: @
        statusCode:
          201: ->
            @httpCreated(arguments)
          200: ->
            @httpOk(arguments, form);
          400: ->
            @httpBadRequest(arguments, form);
          403: ->
            @httpForbidden(arguments, form);
    else if !form.hasClass('HTML5Validation')
      form.addClass('HTML5Validation')
      form.find(':submit').click()
    else
      form.removeClass('HTML5Validation')
}
