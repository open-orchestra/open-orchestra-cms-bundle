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
