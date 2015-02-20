showVideoForm = (videoType) ->
  $('#form-youtube, #form-dailymotion, #form-vimeo').appendTo $('#inactive-form-part')
  $('#form-' + videoType).appendTo '#active-form-part'
  return

$(document).on 'change', '#block_videoType', (event) ->
  showVideoForm $(this).val()
  return
