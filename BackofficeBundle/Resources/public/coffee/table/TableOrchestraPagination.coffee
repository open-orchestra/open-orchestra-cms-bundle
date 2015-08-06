# DataTable Pagination
$.extend $.fn.dataTableExt.oPagination, 'input_full':
  'fnInit': (oSettings, nPaging, fnDraw) ->
    oLang = oSettings.oLanguage.oPaginate
    fnClickHandler = (e) ->
      e.preventDefault()
      if oSettings.oApi._fnPageChange(oSettings, e.data.action)
        fnDraw oSettings
      return
    $(nPaging).append '<ul class="pagination pagination-sm">' + '<li class="prev disabled"><a href="#">&larr; ' + oLang.sPrevious + '</a></li>' + '<li class="next disabled"><a href="#">' + oLang.sNext + ' &rarr; </a></li>' + '</ul>'
    els = $('a', nPaging)
    $(els[0]).bind 'click.DT', { action: 'previous' }, fnClickHandler
    $(els[1]).bind 'click.DT', { action: 'next' }, fnClickHandler
    return

  'fnUpdate': (oSettings, fnDraw) ->
    fnUpdatePage = (e) ->
      e.preventDefault()
      startPage = (parseInt($('a', this).text(), 10) - 1)
      if startPage >= 0
        oSettings._iDisplayStart = startPage * oPaging.iLength
        fnDraw oSettings
      return
    insertLinkPage = (number, i, cssClass) ->
      cssClass = if cssClass? then 'class=' + cssClass  else ''
      link = $('<li ' + cssClass + '><a href="#">' + number + '</a></li>').bind 'click', fnUpdatePage
      link.insertBefore($('li:last', an[i])[0])

    iListLength = 5
    oPaging = oSettings.oInstance.fnPagingInfo()
    an = oSettings.aanFeatures.p
    i = undefined
    ien = undefined
    j = undefined
    sClass = undefined
    iStart = undefined
    iEnd = undefined
    iHalf = Math.floor(iListLength / 2)
    if oPaging.iTotalPages < iListLength
      iStart = 1
      iEnd = oPaging.iTotalPages
    else if oPaging.iPage <= iHalf
      iStart = 1
      iEnd = iListLength
    else if oPaging.iPage >= oPaging.iTotalPages - iHalf
      iStart = oPaging.iTotalPages - iListLength + 1
      iEnd = oPaging.iTotalPages
    else
      iStart = oPaging.iPage - iHalf + 1
      iEnd = iStart + iListLength - 1

    i = 0
    ien = an.length
    while i < ien
# Remove the middle elements
      $('li:gt(0)', an[i]).filter(':not(:last)').remove()
      # Add the new list items and their event handlers
      j = iStart
      if iStart > 1
        insertLinkPage('1', i)
        insertLinkPage('...', i, 'disabled')
      while j <= iEnd
        if iStart + iHalf + 1 == j && j != oPaging.iTotalPages
          liInput = $('<li ' + sClass + '></li>')
          input = $('<input type="integer" value='+j+'>')
          input.keyup (e) ->
            e.preventDefault()
            startPage = (parseInt($(this).val()) - 1)
            if startPage > 0
              oSettings._iDisplayStart = startPage * oPaging.iLength
              fnDraw oSettings
            return
          liInput.html(input)
          liInput.insertBefore($('li:last', an[i])[0])
        else
          cssClass = if j == oPaging.iPage + 1 then 'active' else ''
          insertLinkPage(j, i, cssClass)
        j++
      if j < oPaging.iTotalPages + 1
        insertLinkPage('...', i, 'disabled')
        insertLinkPage(oPaging.iTotalPages, i)
      # Add / remove disabled classes from the static elements
      if oPaging.iPage == 0
        $('li:first', an[i]).addClass 'disabled'
      else
        $('li:first', an[i]).removeClass 'disabled'
      if oPaging.iPage == oPaging.iTotalPages - 1 or oPaging.iTotalPages == 0
        $('li:last', an[i]).addClass 'disabled'
      else
        $('li:last', an[i]).removeClass 'disabled'
      i++
    return
