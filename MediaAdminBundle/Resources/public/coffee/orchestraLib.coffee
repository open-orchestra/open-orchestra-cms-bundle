#ACTIVATE TINYMCE
callback_tinymce_init = null
doCallBack = (editor, view) ->
  $.extend true, view, extendView['orchestraMediaAbstractType'], extendView['orchestraWysiwygType']
  target = editor.id + '_modal'
  $('#' + editor.editorContainer.id + ' .mce-btn[aria-label="mediamanager"] button').data
    target: target
    input: editor.id
    url: $('#' + target).data('url')
  view.delegateEvents()
  return
