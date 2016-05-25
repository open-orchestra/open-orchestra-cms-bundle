# ACTIVATE TINYMCE

callback_tinymce_init = null

tinymce_button_linkmanager = (editor) ->
  internalLinkFormViewClass = appConfigurationView.getConfiguration('internalLink', 'showForm')
  new internalLinkFormViewClass($.extend($('#modal_internal_link_' + editor.id).data(), editor: editor))

doCallBack = (editor, view) ->

$(document).on('focusin', (e) ->
  if ($(e.target).closest(".mce-window").length)
    e.stopImmediatePropagation();
)
