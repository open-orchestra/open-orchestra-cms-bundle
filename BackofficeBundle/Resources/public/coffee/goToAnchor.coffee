
$("document").ready ->
  hash = self.location.hash
  if hash isnt ""
    nodeId = hash.substr hash.lastIndexOf("#") + 1
    $(this).find("li").each ->
      if $(this).data("tree-parameter") is nodeId
        openMenu($(this), 'page')
        $(this).find("a:first").click();
      return
  return

openMenu = ($this, page)->
  unless $this.data("tree-parameter") is page
    $this.parent("ul").toggle()
    openMenu $this.parent("ul").parent("li"), page
  return
