generateSiteId = ->
  $("input.site-id-dest").val $("input.site-id-source").val().latinise().replace(/[^a-z0-9]/gi,'_')
  return

stopGenerateSiteId = ->
  $("input.site-id-dest").unbind()
