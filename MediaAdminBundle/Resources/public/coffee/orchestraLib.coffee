#ACTIVATE TINYMCE
callback_tinymce_init = null
activateTinyMce = (view, isDisabled) ->
  tinymce.editors = []
  do (view) ->
    callback_tinymce_init = (editor) ->
      $.extend true, view, extendView['orchestraMediaAbstractType'], extendView['orchestraWysiwygType']
      target = editor.id + '_modal'
      $('#' + editor.editorContainer.id + ' .mce-btn[aria-label="mediamanager"] button').data
        target: target
        input: editor.id
        url: $('#' + target).data('url')
      view.delegateEvents()
      return
    return
  if !isDisabled
    initTinyMCE()
  else
    initTinyMCE($.extend(true, {}, stfalcon_tinymce_config, {theme: {simple: {readonly: 1}}}))
