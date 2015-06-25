#ACTIVATE TINYMCE
callback_tinymce_init = null
doCallBack = (view, textarea) ->
  isRequired = textarea.attr('required') == 'required'
  do (view, textarea, isRequired) ->
    callback_tinymce_init = (editor) ->
      if isRequired
        textarea.attr('required', 'required')
      $.extend true, view, extendView['orchestraMediaAbstractType'], extendView['orchestraWysiwygType']
      target = editor.id + '_modal'
      $('#' + editor.editorContainer.id + ' .mce-btn[aria-label="mediamanager"] button').data
        target: target
        input: editor.id
        url: $('#' + target).data('url')
      view.delegateEvents()
      return
    return
