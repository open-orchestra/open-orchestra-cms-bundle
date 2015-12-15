# DataTable Pagination input full
$.extend $.fn.dataTableExt.oPagination, 'input_full':
  'fnInit': (settings, pagingElement, drawCallback) ->
    lang = settings.oLanguage.oPaginate
    clickHandler = (e) ->
      e.preventDefault()
      if settings.oApi._fnPageChange(settings, e.data.action)
        drawCallback settings
      return
    $(pagingElement).append '<ul class="pagination pagination-sm">' + '<li class="prev disabled"><a href="#">&larr; ' + lang.sPrevious + '</a></li>' + '<li class="next disabled"><a href="#">' + lang.sNext + ' &rarr; </a></li>' + '</ul>'
    els = $('a', pagingElement)
    $(els[0]).bind 'click.DT', { action: 'previous' }, clickHandler
    $(els[1]).bind 'click.DT', { action: 'next' }, clickHandler
    return

  'fnUpdate': (settings, drawCallback) ->
    fnUpdatePage = (e) ->
      e.preventDefault()
      startPage = (parseInt($('a', this).text(), 10) - 1)
      if startPage >= 0
        settings._iDisplayStart = startPage * paging.length
        drawCallback settings
      return
    insertLinkPage = (number, i, cssClass) ->
      cssClass = if cssClass? then 'class=' + cssClass  else ''
      link = $('<li ' + cssClass + '><a href="#">' + number + '</a></li>').bind 'click', fnUpdatePage
      link.insertBefore($('li:last', domP[i])[0])

    listLength = 5
    paging = settings.oInstance.api().table().page.info()
    domP = settings.aanFeatures.p
    i = undefined
    j = undefined
    start = undefined
    end = undefined
    half = Math.floor(listLength / 2)
    if paging.pages < listLength
      start = 1
      end = paging.pages
    else if paging.page <= half
      start = 1
      end = listLength
    else if paging.page >= paging.pages - half
      start = paging.pages - listLength + 1
      end = paging.pages
    else
      start = paging.page - half + 1
      end = start + listLength - 1

    i = 0
    #for multiple pagination
    while i < domP.length
      # Remove the middle elements
      $('li:gt(0)', domP[i]).filter(':not(:last)').remove()
      # Add the new list items and their event handlers
      j = start
      if start > 1
        insertLinkPage('1', i)
        insertLinkPage('...', i, 'disabled')
      while j <= end
        if start + half + 1 == j && j != paging.pages
          liInput = $('<li></li>')
          input = $('<input type="integer" value='+j+'>')
          input.keyup (e) ->
            e.preventDefault()
            startPage = (parseInt($(this).val()) - 1)
            if startPage > 0
              settings._iDisplayStart = startPage * paging.length
              drawCallback settings
            return
          liInput.html(input)
          liInput.insertBefore($('li:last', domP[i])[0])
        else
          cssClass = if j == paging.page + 1 then 'active' else ''
          insertLinkPage(j, i, cssClass)
        j++
      if j < paging.pages + 1
        insertLinkPage('...', i, 'disabled')
        insertLinkPage(paging.pages, i)
      # Add / remove disabled classes from the static elements
      if paging.page == 0
        $('li:first', domP[i]).addClass 'disabled'
      else
        $('li:first', domP[i]).removeClass 'disabled'
      if paging.page == paging.pages - 1 or paging.pages == 0
        $('li:last', domP[i]).addClass 'disabled'
      else
        $('li:last', domP[i]).removeClass 'disabled'
      i++
    return
