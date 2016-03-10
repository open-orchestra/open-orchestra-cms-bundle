###*
 * @namespace OpenOrchestra:FormBehavior
###
window.OpenOrchestra or= {}
window.OpenOrchestra.FormBehavior or= {}

###*
 * @class TinyMce
###
class OpenOrchestra.FormBehavior.TinyMce extends OpenOrchestra.FormBehavior.AbstractFormBehavior

  ###*
   * activateBehaviorOnElements
   * @param {Array} elements
   * @param {Object} view
   * @param {Object} form
  ###
  activateBehaviorOnElements: (elements, view, form) ->
    textareaId = []
    elements.each ->
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

    do (view, elements) ->
      elements.filter('[required="required"]').data('required', true)

      window.callback_tinymce_init = (editor) ->
        elements.each ->
          if $(this).data('required')
            $(this).attr('required', 'required')
        elements.addClass('focusable')
        doCallBack(editor, view)
        return
      return

    if elements.attr('disabled') == 'disabled'
      initTinyMCE($.extend(true, {}, stfalcon_tinymce_config, {theme: {simple: {readonly: 1}}}))
    else
      initTinyMCE()

formBehaviorLibrary.add(new OpenOrchestra.FormBehavior.TinyMce("textarea.tinymce"))
