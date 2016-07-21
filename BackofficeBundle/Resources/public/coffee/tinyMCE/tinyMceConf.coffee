# ACTIVATE TINYMCE

callback_tinymce_init = null

tinymce_button_linkmanager = (editor) ->
  selection = $(editor.selection.getNode())
  fields = if typeof selection.data('options') != 'undefined' then selection.data('options') else {}
  internalLinkFormViewClass = appConfigurationView.getConfiguration('internalLink', 'showForm')
  new internalLinkFormViewClass($.extend($('#modal_internal_link_' + editor.id).data(), editor: editor, fields: fields))

doCallBack = (editor, view) ->

$(document).on('focusin', (e) ->
  if ($(e.target).closest(".mce-window").length)
    e.stopImmediatePropagation();
)
