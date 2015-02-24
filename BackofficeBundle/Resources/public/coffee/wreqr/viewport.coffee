viewportChannel = Backbone.Wreqr.radio.channel('viewport')

viewportChannel.commands.setHandler 'init', (blockpanel) ->
  $(this).data 'blockpanel', blockpanel

viewportChannel.commands.setHandler 'resize', ->
  topDiv = $('<div class="panel-fixed" />').css('display', 'none')
  $(this).data('blockpanel').append(topDiv)
  minTop = parseInt topDiv.css('top')
  topDiv.remove()
  $(this).data('blockpanel').height $(window).height() - minTop
  $(this).data 'minTop', minTop
  Backbone.Wreqr.radio.commands.execute 'viewport', 'scroll'
  return

viewportChannel.commands.setHandler 'scroll', ->
  $(this).data('blockpanel').removeClass('panel-fixed')
  rect = $(this).data('blockpanel')[0].getBoundingClientRect()
  if rect.top <= $(this).data('minTop')
    $(this).data('blockpanel').addClass('panel-fixed')
  else
    $(this).data('blockpanel').removeClass('panel-fixed')
  return
