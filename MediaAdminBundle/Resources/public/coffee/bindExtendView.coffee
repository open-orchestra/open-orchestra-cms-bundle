callback_tinymce_init = null
widgetChannel.bind 'ready', (view) ->
  if $('[data-prototype*=\'mediaModalOpen\']', view.$el).length > 0
    $.extend true, view, extendView['orchestraMediaAbstractType'], extendView['orchestraMediaType']
    view.delegateEvents()
  if $('.tinymce', view.$el).length > 0
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
  return