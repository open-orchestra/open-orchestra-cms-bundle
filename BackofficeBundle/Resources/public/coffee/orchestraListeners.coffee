$(".modal-dialog").on "resize", (e) ->
  $(this).prev().height($(this).parent().height())
  return

# CLOSE MODALS
$(".close", ".modal-header").click ->
  $("#select2-drop-mask").click();
  return

$(".configuration-change").click ->
  url = $(this).data('url')
  window.location = url + '#' + Backbone.history.fragment
