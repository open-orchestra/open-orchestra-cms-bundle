html2bbcodeConfigurator = ->
  finalTransformation :
    '"'                                : '&quot;'          ,
    '>'                                : '&gt;'            ,
    '<'                                : '&lt;'            ,
    ' '                                : '&nbsp;'

  transformation :
    '<img.*?src="([^"]*)" alt="([^"]*)" width="([0-9]*)" height="([0-9]*)"\\/>' : '[img alt="$2" width="$3" height="$4"]$1[/img]',
    '<img.*?src="([^"]*)" width="([0-9]*)" height="([0-9]*)"\\/>' : '[img width="$2" height="$3"]$1[/img]',
    '<img.*?src="([^"]*)" alt="([^"]*)" height="([0-9]*)"\\/>' : '[img alt="$2" height="$3"]$1[/img]',
    '<img.*?src="([^"]*)" alt="([^"]*)" width="([0-9]*)"\\/>' : '[img alt="$2" width="$3"]$1[/img]',
    '<img.*?src="([^"]*)" alt="([^"]*)" title="([^"]*)"\\/>' : '[img alt="$2" title="$3"]$1[/img]',
    '<img.*?src="([^"]*)" height="([0-9]*)"\\/>' : '[img height="$2"]$1[/img]',
    '<img.*?src="([^"]*)" width="([0-9]*)"\\/>' : '[img width="$2"]$1[/img]',
    '<img.*?src="([^"]*)" title="([^"]*)"\\/>' : '[img title="$2"]$1[/img]',
    '<img.*?src="([^"]*)" alt="([^"]*)"\\/>' : '[img alt="$2"]$1[/img]',
    '<a href="([^"]*)" target="([^"]*)" title="([^"]*)">(.*?)<\\/a>' : '[url href="$1" target="$2" title="$3"]$4[/url]',
    '<a href="([^"]*)" target="([^"]*)">(.*?)<\\/a>' : '[url href="$1" target="$2"]$3[/url]',
    '<a href="([^"]*)" title="([^"]*)">(.*?)<\\/a>' : '[url href="$1" title="$2"]$3[/url]',
    '<a href="([^"]*)">(.*?)<\\/a>' : '[url="$1"]$2[/url]',
    '<td style="([^"]*)" scope="([^"]*)">' : '[td style="$1" scope="$2"]',
    '<tr style="([^"]*)" scope="([^"]*)">' : '[tr style="$1" scope="$2"]',
    '<th style="([^"]*)" scope="([^"]*)">' : '[th style="$1" scope="$2"]',
    '<u>'                              : '[u]'             ,
    '<\\/u>'                           : '[/u]'            ,
    '<em>'                             : '[i]'             ,
    '<\\/em>'                          : '[/i]'            ,
    '<strong>'                         : '[b]'             ,
    '<\\/strong>'                      : '[/b]'            ,
    '<p style="([^"]*)">'              : '[p="$1"]'        ,
    '<\\/p>'                           : '[/p]'            ,
    '<p>'                              : '[p]'             ,
    '<br \\/>'                         : '[br]'            ,
    '<br\\/>'                          : '[br]'            ,
    '<br>'                             : '[br]'            ,
    '<\\/h([0-6])>'                    : '[/h$1]'          ,
    '<h([0-6])>'                       : '[h$1]'           ,
    '<\\/code>'                        : '[/code]'         ,
    '<code>'                           : '[code]'          ,
    '<ul>'                             : '[ul]'            ,
    '<ul style="([^"]*)">'             : '[ul="$1"]'       ,
    '<\\/ul>'                          : '[/ul]'           ,
    '<ol>'                             : '[ol]'            ,
    '<ol style="([^"]*)">'             : '[ol="$1"]'       ,
    '<\\/ol>'                          : '[/ol]'           ,
    '<li>'                             : '[li]'            ,
    '<\\/li>'                          : '[/li]'           ,
    '<span>'                           : '[span]'          ,
    '<span style="([^"]*)">'           : '[span="$1"]'     ,
    '<\\/span>'                        : '[/span]'         ,
    '<\\/pre>'                         : '[/pre]'          ,
    '<pre>'                            : '[pre]'           ,
    '<\\/blockquote>'                  : '[/blockquote]'   ,
    '<blockquote>'                     : '[blockquote]'    ,
    '<\\/div>'                         : '[/div]'          ,
    '<div>'                            : '[div]'           ,
    '<\\/sup>'                         : '[/sup]'          ,
    '<sup>'                            : '[sup]'           ,
    '<\\/sub>'                         : '[/sub]'          ,
    '<sub>'                            : '[sub]'           ,
    '<\\/table>'                       : '[/table]'        ,
    '<table style="([^"]*)">'          : '[table="$1"]'    ,
    '<table>'                          : '[table]'         ,
    '<\\/tbody>'                       : '[/tbody]'        ,
    '<tbody>'                          : '[tbody]'         ,
    '<\\/td>'                          : '[/td]'           ,
    '<td>'                             : '[td]'            ,
    '<td style="([^"]*)">'             : '[td="$1"]'       ,
    '<\\/tr>'                          : '[/tr]'           ,
    '<tr>'                             : '[tr]'            ,
    '<tr style="([^"]*)">'             : '[tr="$1"]'       ,
    '<\\/th>'                          : '[/th]'           ,
    '<th>'                             : '[th]'            ,
    '<th style="([^"]*)">'             : '[th="$1"]'

  getTransformation: () ->
    concatTransformation = {}
    $.extend concatTransformation, @transformation
    $.extend concatTransformation, @finalTransformation
    return concatTransformation

  addTransformation: (tra) ->
    $.extend @transformation, tra
    return

html2bbcode = new html2bbcodeConfigurator()
