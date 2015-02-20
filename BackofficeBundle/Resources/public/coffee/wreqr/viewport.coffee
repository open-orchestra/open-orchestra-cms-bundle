viewportChannel = Backbone.Wreqr.radio.channel('viewport')
viewportChannel.commands.setHandler 'init', (blockpanel) ->
  minTop = $('#ribbon')[0].offsetHeight + $('#ribbon')[0].offsetTop
  gap = $(window).scrollTop() > minTop
  blockpanel.height $(window).height() - minTop
  $(this).data 'minTop', minTop
  $(this).data 'gap', gap
  return
viewportChannel.commands.setHandler 'change', (blockpanel) ->
  gap = $(window).scrollTop() > $(this).data('minTop')
  if gap != $(this).data('gap')
    $(this).data 'gap', gap
    if gap
      blockpanel.css 'position', 'fixed'
      blockpanel.css 'top', $(this).data('minTop')
    else
      blockpanel.css 'position', 'absolute'
      blockpanel.css 'top', '0px'
  return
