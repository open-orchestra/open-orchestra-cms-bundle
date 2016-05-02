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
      elements.filter('[required="required"]').data('required', true)
      window.callback_tinymce_init = (editor) ->
        elements.each ->
          if $(this).data('required')
            $(this).attr('required', 'required')
        elements.addClass('focusable')
        doCallBack(editor, view)
        return
      return
    context = @
    elements.each ->
      if($('#' + $(this).attr('id')).length == 1)
        parameters = $.extend(true, {}, stfalcon_tinymce_config, {selector: '#' + $(this).attr('id')})
        if $(this).attr('disabled') == 'disabled'
          parameters = $.extend(true, {}, stfalcon_tinymce_config, {selector: '#' + $(this).attr('id'), theme: {simple: {readonly: 1}}})
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
      if $('#' + tinymce.editors[i].id, form).length == 1
        tinymce.EditorManager.execCommand 'mceRemoveEditor', false, tinymce.editors[i].id

  ###*
   * initOOTinyMCE
   * @param {Array} options
  ###
  initOOTinyMCE: (options) ->
    if typeof tinymce == 'undefined'
      return false
    if typeof options == 'undefined'
      options = stfalcon_tinymce_config
    domready ->
      t = tinymce.editors
      textareas = []
      switch options.selector.substring(0, 1)
        when '#'
          _t = document.getElementById(options.selector.substring(1))
          if _t
            textareas.push _t
        when '.'
          textareas = getElementsByClassName(options.selector.substring(1), 'textarea')
        else
          textareas = document.getElementsByTagName('textarea')
      if !textareas.length
        return false
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
      i = 0
      while i < textareas.length
        theme = textareas[i].getAttribute('data-theme') or 'simple'
        settings = if typeof options.theme[theme] != 'undefined' then options.theme[theme] else options.theme['simple']
        settings.external_plugins = settings.external_plugins or {}
        p = 0
        while p < externalPlugins.length
          settings.external_plugins[externalPlugins[p]['id']] = externalPlugins[p]['url']
          p++
        if textareas[i].getAttribute('required') != ''
          textareas[i].removeAttribute 'required'
        if textareas[i].getAttribute('id') == ''
          textareas[i].setAttribute 'id', 'tinymce_' + Math.random().toString(36).substr(2)
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
        tinymce.EditorManager.execCommand 'mceRemoveEditor', false, textareas[i].getAttribute('id')
        tinymce.init $.extend({}, settings, selector: '#' + textareas[i].getAttribute('id'))
        i++
      return
    return

jQuery ->
  OpenOrchestra.FormBehavior.formBehaviorLibrary.add(new OpenOrchestra.FormBehavior.RichText("textarea.tinymce"))
