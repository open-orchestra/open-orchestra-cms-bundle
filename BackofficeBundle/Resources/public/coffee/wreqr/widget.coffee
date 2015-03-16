widgetChannel = Backbone.Wreqr.radio.channel('widget')

widgetChannel.commands.setHandler 'init', (view) ->
  if view.options
    if view.options.multiLanguage
      Backbone.Wreqr.radio.commands.execute 'language', 'init', view
    if view.options.multiStatus
      Backbone.Wreqr.radio.commands.execute 'status', 'init', view
    if view.options.multiVersion
      Backbone.Wreqr.radio.commands.execute 'version', 'init', view
    if view.options.duplicate
      Backbone.Wreqr.radio.commands.execute 'duplicate', 'init', view
  return

widgetChannel.commands.setHandler 'ready', (view) ->
  if view.options
    if view.options.multiLanguage
      Backbone.Wreqr.radio.commands.execute 'language', 'ready', view
    if view.options.multiStatus
      Backbone.Wreqr.radio.commands.execute 'status', 'ready', view
    if view.options.multiVersion
      Backbone.Wreqr.radio.commands.execute 'version', 'ready', view
  return
  
widgetChannel.commands.setHandler 'loaded', (el) ->
  if $(".select2", el).length > 0
    activateSelect2($(".select2", el))
  if $(".orchestra-node-choice", el).length > 0
    activateOrchestraNodeChoice($(".orchestra-node-choice", el))
  if $(".colorpicker", el).length > 0
    activateColorPicker($(".colorpicker", el))
  if $(".helper-block", el).length > 0
    activateHelper($(".helper-block", el))
