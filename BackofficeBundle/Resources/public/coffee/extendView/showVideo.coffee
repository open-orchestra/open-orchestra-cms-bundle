extendView = extendView || {}
extendView['showVideo'] = {
  events:
    'change #oo_block_videoType': 'showVideoForm'

  showVideoForm: (event) ->
    $('#form-youtube, #form-dailymotion, #form-vimeo').appendTo $('#inactive-form-part')
    $('#form-' + $(event.target).val()).appendTo '#active-form-part'
    return
}
