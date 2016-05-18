###*
 * @namespace OpenOrchestra:FormBehavior
###
window.OpenOrchestra or= {}
window.OpenOrchestra.FormBehavior or= {}

###*
 * @class RichText
###
class OpenOrchestra.FormBehavior.RichText extends OpenOrchestra.FormBehavior.AbstractFormBehavior

  ###*
   * activateBehaviorOnElements
   * @param {Array} elements
   * @param {Object} view
   * @param {Object} form
  ###
  activateBehaviorOnElements: (elements, view, form) ->
    $.ajax
      url: $("#contextual-informations").data("translation-url-pattern").replace("*domain*", "tinymce")
      success: (response) ->
        tinymce.util.I18n.add response.locale, response.catalog

    do (view, elements) ->
      window.callback_tinymce_init = (editor) ->
        elements.addClass('focusable')
        doCallBack(editor, view)
        return
      return
    context = @
    elements.each ->
      if($('#' + $(this).attr('id')).length == 1)
        parameters = $.extend(true, {}, stfalcon_tinymce_config, {selector: '#' + $(this).attr('id')})
        if $(this).attr('disabled') == 'disabled'
          parameters = $.extend(true, parameters, {theme: {simple: {readonly: 1}}})
        context.initOOTinyMCE(parameters)
    $.each(tinymce.editors, (key, editor) ->
        editor.on 'change', (event) ->
          @.save();
    )

  ###*
   * deactivateBehaviorOnElements
   * @param {Array} elements
   * @param {Object} view
   * @param {Object} form
  ###
  deactivateBehaviorOnElements: (elements, view, form) ->
    for i of tinymce.editors
      id = tinymce.editors[i].id
      if $('#' + id, form).length == 1
        tinymce.EditorManager.remove tinymce.EditorManager.get(id)

  ###*
   * initOOTinyMCE
   * @param {Array} options
  ###
  initOOTinyMCE: (options) ->
    if typeof tinymce == 'undefined'
      return false
    textarea = $(options.selector)[0]
    externalPlugins = []
    if typeof options.external_plugins == 'object'
      for pluginId of options.external_plugins
        if !options.external_plugins.hasOwnProperty(pluginId)
          continue
        opts = options.external_plugins[pluginId]
        url = opts.url or null
        if url
          externalPlugins.push
            'id': pluginId
            'url': url
          tinymce.PluginManager.load pluginId, url
    theme = textarea.getAttribute('data-theme') or 'simple'
    settings = if typeof options.theme[theme] != 'undefined' then options.theme[theme] else options.theme['simple']
    settings.external_plugins = settings.external_plugins or {}
    p = 0
    while p < externalPlugins.length
      settings.external_plugins[externalPlugins[p]['id']] = externalPlugins[p]['url']
      p++
    if typeof options.tinymce_buttons == 'object'

      settings.setup = (editor) ->
        for buttonId of options.tinymce_buttons
          if !options.tinymce_buttons.hasOwnProperty(buttonId)
            p++
            continue
          ((id, opts) ->

            opts.onclick = ->
              callback = window['tinymce_button_' + id]
              if typeof callback == 'function'
                callback editor
              else
                alert 'You have to create callback function: "tinymce_button_' + id + '"'
              return

            editor.addButton id, opts
            return
          ) buttonId, clone(options.tinymce_buttons[buttonId])
        if options.use_callback_tinymce_init
          editor.on 'init', ->
            callback = window['callback_tinymce_init']
            if typeof callback == 'function'
              callback editor
            else
              alert 'You have to create callback function: callback_tinymce_init'
            return
        return
      tinymce.EditorManager.init $.extend({}, settings, selector: options.selector)
      return
    return

jQuery ->
  OpenOrchestra.FormBehavior.formBehaviorLibrary.add(new OpenOrchestra.FormBehavior.RichText("textarea.tinymce"))
