((html2bbcode) ->
  internalLinkTransformation =
    '<a.*?data-options="([^"]*)".*?>(.*?)<\/a>' : '[link=$1]$2[/link]'

  html2bbcode.addTransformation internalLinkTransformation
) window.html2bbcode
