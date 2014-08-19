
$(".ajax-remove-hash").click (e) ->
  e.preventDefault()
  self.location.hash = ''
  return
