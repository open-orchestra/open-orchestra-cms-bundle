
$("document").ready ->
  hash = self.location.hash
  goToAnchor($(this), hash)
  return

$(window).on "hashchange", ->
  hash = self.location.hash
  goToAnchor($(document), hash)
  return

goToAnchor = ($this, hash)->
  if hash isnt ""
    nodeId = hash.substr hash.lastIndexOf("#") + 1
    $this.find("li").each ->
      if $(this).data("element-id") is nodeId

        openMenu $(this), 'page'
        showNode $(this).find("a:first").data("url") if $(this).data("type") is "Node"
        showTemplate $(this).find("a:first").data("url") if $(this).data("type") is "Template"
      return
  return


openMenu = ($this, page)->
  unless $this.data("element-id") is page
    $this.parent("ul").show()
    openMenu $this.parent("ul").parent("li"), page
  return
