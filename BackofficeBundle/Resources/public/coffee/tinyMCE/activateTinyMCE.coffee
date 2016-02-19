# ACTIVATE TINYMCE

callback_tinymce_init = null

tinymce_button_linkmanager = null

doCallBack = (editor, view) ->

initParameter = () ->

$(document).on('focusin', (e) ->
  if ($(e.target).closest(".mce-window").length)
    e.stopImmediatePropagation();
)

activateTinyMce = (view, textarea) ->
  textareaId = []
  textarea.each ->
    textareaId.push @id
    return
  $.each tinymce.editors, (index, editor) ->
    if $('#' + editor.id).length == 0 or $.inArray(editor.id, textareaId) != -1
      delete tinymce.editors[index]
    return
  tinymce.editors = tinymce.editors.filter (e) ->
    e != undefined
  $.ajax
    url: $("#contextual-informations").data("translation-url-pattern").replace("*domain*", "tinymce")
    success: (response) ->
      tinymce.util.I18n.add response.locale, response.catalog

  do (view, textarea) ->
    initParameter()
    tinymce.create 'tinymce.plugins.BBCodeOrchestraPlugin',
      init: (ed) ->
        self = @
        ed.on 'beforeSetContent', (e) ->
          e.content = self.bbcodeToHtml e.content
          return
        ed.on 'postProcess', (e) ->
          if e.set
            e.content = self.bbcodeToHtml e.content
          if e.get
            e.content = self.htmlToBBcode e.content
          return
        ed.on 'submit', (e) ->
          e.content = self.htmlToBBcode e.content
          return
        return
      bbcodeToHtml: (string) ->
        @punbbConvert string, bbcode2html.getTransformation()

      htmlToBBcode: (string) ->
        @punbbConvert string, html2bbcode.getTransformation()

      punbbConvert: (string, replacementList) ->
        string = tinymce.trim(string)
        rep = (re, str) ->
          string = string.replace(new RegExp(re,'gi'), str)
          return
        for regex, str of replacementList
          rep regex, str
        string
      getInfo : () ->
        longname : 'bbCode conversion plugin',
        author : 'open orchestra',
        infourl : 'www.open-orchestra.com',
        version : "1.0"
    # Register plugin
    tinymce.PluginManager.add('orchestra_bbcode', tinymce.plugins.BBCodeOrchestraPlugin);

    textarea.filter('[required="required"]').data('required', true)

    callback_tinymce_init = (editor) ->
      tinymce_button_linkmanager = (editor) ->
        internalLinkFormViewClass = appConfigurationView.getConfiguration('internalLink', 'showForm')
        new internalLinkFormViewClass($.extend($('#modal_internal_link_' + editor.id).data(), editor: editor))
      textarea.each ->
        if $(this).data('required')
          $(this).attr('required', 'required')
      textarea.addClass('focusable')
      doCallBack(editor, view)
      return
    return

  if textarea.attr('disabled') == 'disabled'
    initTinyMCE($.extend(true, {}, stfalcon_tinymce_config, {theme: {simple: {readonly: 1}}}))
  else
    initTinyMCE()
