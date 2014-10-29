renderPageTitle = ->
  $('#title-logo').addClass($('nav li.active:first > a > i').attr('class').replace('fa-lg', ''))
  $('#title-universe').text($('nav li.active:first > a > span').text())
  $('#title-functionnality').text('> ' + $('.breadcrumb li:last').text())
  