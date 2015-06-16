callback_tinymce_init = null
widgetChannel.bind 'ready', (view) ->
  isMediaView = $("[data-prototype*='clear-media']", view.$el).length > 0
  wysiwygModal = $(".select_media_modal", view.$el)
  isWysiwygView = wysiwygModal.length > 0
  if isMediaView or isWysiwygView
    $.extend true, view, extendView['orchestraMediaAbstractType']
    if isMediaView
      $.extend true, view, extendView['orchestraMediaType']
      return view.delegateEvents()
    if isWysiwygView
      do (view) ->
        callback_tinymce_init = (editor) ->
          $.extend true, view, extendView['orchestraWysiwygType']
          mediaManager = $("#" + editor.editorContainer.id + ' .mce-btn[aria-label="mediamanager"] button')
          mediaManager.attr("data-target", "select_media_modal" )
          mediaManager.attr("data-input", editor.id)
          mediaManager.attr("data-url", wysiwygModal.data("url"))
          return view.delegateEvents()
  return
