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

  http_ok: (response, form) ->
    @openForm(response, form)
    
  http_bad_request: (response, form) ->
    widgetChannel.trigger 'form-error', @
    @openForm(error.responseText, form)
  
  http_created: (response, form) ->
    widgetChannel.trigger 'element-created', @
    displayRoute = $("#nav-" + @options.entityType).attr('href')
    Backbone.history.navigate(displayRoute, {trigger: true})
    viewClass = appConfigurationView.getConfiguration(@options.entityType, 'showFlashBag')
    new viewClass(@addOption(
      html: response
      domContainer: $('h1.page-title').parent()
    ))
    $(document).scrollTop 0

  http_forbidden: (response, form) ->
    displayRoute = OpenOrchestra.ForbiddenAccessRedirection[Backbone.history.fragment]
    if typeof displayRoute != 'undefined'
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
          201: (response) ->
            @http_created(response, form)
          200: (response) ->
            @http_ok(response, form);
          400: (error) ->
            @http_bad_request(response, form);
          403: (response) ->
    else if !form.hasClass('HTML5Validation')
      form.addClass('HTML5Validation')
      form.find(':submit').click()
    else
      form.removeClass('HTML5Validation')
}
