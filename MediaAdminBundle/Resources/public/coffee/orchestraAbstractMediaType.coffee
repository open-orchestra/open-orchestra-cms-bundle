extendView = extendView || {}
extendView['orchestraMediaAbstractType'] =
  events:
    'click .clear-media': 'clearMedia'

  clearMedia: (event) ->
    event.preventDefault()
    target = $(event.currentTarget)
    inputId = '#' + target.data('input')
    previewId = '#previewImage_' + target.data('input')
    $(inputId).val ''
    $(previewId).removeAttr 'src'

  openMediaModal: (modal, inputId, url, method) ->
    @abstractOpenMediaModal(modal, inputId, url, method)
    return

  abstractOpenMediaModal: (modal, inputId, url, method, mediaModalView) ->
    mediaModalView = mediaModalView or "showMediaView"
    viewClass = appConfigurationView.getConfiguration('media', mediaModalView)
    new viewClass(
      body: "<h1><i class=\"fa fa-cog fa-spin\"></i> Loading...</h1>"
      domContainer: modal
      input: inputId
    )
    $.ajax
      url: url
      method: method
      success: (response) ->
        new viewClass(
          body: response
          domContainer: modal
          input: inputId
        )
      error: ->
        new viewClass(
          body: 'Erreur durant le chargement'
          domContainer: modal
          input: inputId
        )
    return

callback_tinymce_init = null
widgetChannel.bind 'ready', (view) ->
  isMediaView = $("[data-prototype*='clear-media']", view.$el).length > 0
  isWysiwygView = $(".select_media_modal", view.$el).length > 0
  if isMediaView or isWysiwygView
    $.extend true, view, extendView['orchestraMediaAbstractType']
    if isMediaView
      $.extend true, view, extendView['orchestraMediaType']
    if isWysiwygView
      do (view) ->
        callback_tinymce_init = (editor) ->
          $.extend true, view, extendView['orchestraWysiwygType']
          $("#" + editor.editorContainer.id + ' .mce-btn[aria-label="mediamanager"] button').on('click', ->
            view.launchWysiwygModal(editor.id)
          )
    return view.delegateEvents()
  return