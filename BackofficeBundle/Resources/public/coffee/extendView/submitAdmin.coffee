extendView = extendView || {}
extendView['submitAdmin'] = {
  events:
    'click .submit_form': 'addEventOnSave'

  openForm: (response, form) ->
    viewClass = appConfigurationView.getConfiguration(@options.entityType, @options.formView)
    window.OpenOrchestra.FormBehavior.channel.trigger 'deactivate', @, form

    new viewClass(@addOption(
      html: response[0]
      submitted: true
    ))
    
    if $('.tab-content .submit_form.btn-in-ribbon',@.$el).length > 0
      $('.tab-content .submit_form.btn-in-ribbon',@.$el).removeClass('btn-in-ribbon')
      OpenOrchestra.RibbonButton.ribbonFormButtonView.setFocusedView @, '.ribbon-form-button'
    $(document).scrollTop 0

  http_ok: (response, form) ->
    @openForm(response[0], form)
    
  http_bad_request: (response, form) ->
    widgetChannel.trigger 'form-error', @
    @openForm(response[0].responseText, form)
  
  http_created: (response) ->
    widgetChannel.trigger 'element-created', @
    displayRoute = $("#nav-" + @options.entityType).attr('href')
    Backbone.history.navigate(displayRoute, {trigger: true})
    viewClass = appConfigurationView.getConfiguration(@options.entityType, 'showFlashBag')
    new viewClass(@addOption(
      html: response[0]
      domContainer: $('h1.page-title').parent()
    ))
    $(document).scrollTop 0

  http_forbidden: ->
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
          201: ->
            @http_created(arguments)
          200: ->
            @http_ok(arguments, form);
          400: ->
            @http_bad_request(arguments, form);
          403: ->
            @http_forbidden()
    else if !form.hasClass('HTML5Validation')
      form.addClass('HTML5Validation')
      form.find(':submit').click()
    else
      form.removeClass('HTML5Validation')
}
