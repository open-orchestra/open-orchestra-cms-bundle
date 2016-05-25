tinymce.PluginManager.add 'orchestra_link', (editor) ->

  createLinkList = (callback) ->
    ->
      linkList = editor.settings.link_list
      if typeof linkList == 'string'
        tinymce.util.XHR.send
          url: linkList
          success: (text) ->
            callback tinymce.util.JSON.parse(text)
            return
      else if typeof linkList == 'function'
        linkList callback
      else
        callback linkList
      return

  buildListItems = (inputList, itemCallback, startItems) ->

    appendItems = (values, output) ->
      output = output or []
      tinymce.each values, (item) ->
        menuItem = text: item.text or item.title
        if item.menu
          menuItem.menu = appendItems(item.menu)
        else
          menuItem.value = item.value
          if itemCallback
            itemCallback menuItem
        output.push menuItem
        return
      output

    appendItems inputList, startItems or []

  showDialog = (linkList) ->
    data = {}
    selection = editor.selection
    dom = editor.dom
    selectedElm = undefined
    anchorElm = undefined
    initialText = undefined
    win = undefined
    onlyText = undefined
    textListCtrl = undefined
    linkListCtrl = undefined
    relListCtrl = undefined
    targetListCtrl = undefined
    classListCtrl = undefined
    linkTitleCtrl = undefined
    value = undefined

    linkListChangeHandler = (e) ->
      textCtrl = win.find('#text')
      if !textCtrl.value() or e.lastControl and textCtrl.value() == e.lastControl.text()
        textCtrl.value e.control.text()
      win.find('#href').value e.control.value()
      return

    buildAnchorListControl = (url) ->
      anchorList = []
      tinymce.each editor.dom.select('a:not([href])'), (anchor) ->
        id = anchor.name or anchor.id
        if id
          anchorList.push
            text: id
            value: '#' + id
            selected: url.indexOf('#' + id) != -1
        return
      if anchorList.length
        anchorList.unshift
          text: 'None'
          value: ''
        return {
          name: 'anchor'
          type: 'listbox'
          label: 'Anchors'
          values: anchorList
          onselect: linkListChangeHandler
        }
      return

    updateText = ->
      if !initialText and data.text.length == 0 and onlyText
        @parent().parent().find('#text')[0].value @value()
      return

    urlChange = (e) ->
      meta = e.meta or {}
      if linkListCtrl
        linkListCtrl.value editor.convertURL(@value(), 'href')
      tinymce.each e.meta, (value, key) ->
        win.find('#' + key).value value
        return
      if !meta.text
        updateText.call this
      return

    isOnlyTextSelected = (anchorElm) ->
      html = selection.getContent()
      # Partial html and not a fully selected anchor element
      if (/</.test(html) and (!/^<a [^>]+>[^<]+<\/a>$/.test(html) or html.indexOf('href=') == -1)) or (/^\[.*\].*\[\/.*\]$/.test(html))
        return false
      if anchorElm
        nodes = anchorElm.childNodes
        i = undefined
        if nodes.length == 0
          return false
        i = nodes.length - 1
        while i >= 0
          if nodes[i].nodeType != 3
            return false
          i--
      true

    selectedElm = selection.getNode()
    anchorElm = dom.getParent(selectedElm, 'a[href]')
    onlyText = isOnlyTextSelected()
    data.text = initialText = if anchorElm then anchorElm.innerText or anchorElm.textContent else selection.getContent(format: 'text')
    data.href = if anchorElm then dom.getAttrib(anchorElm, 'href') else ''
    if anchorElm
      data.target = dom.getAttrib(anchorElm, 'target')
    else if editor.settings.default_link_target
      data.target = editor.settings.default_link_target
    if value = dom.getAttrib(anchorElm, 'rel')
      data.rel = value
    if value = dom.getAttrib(anchorElm, 'class')
      data['class'] = value
    if value = dom.getAttrib(anchorElm, 'title')
      data.title = value
    if onlyText
      textListCtrl =
        name: 'text'
        type: 'textbox'
        size: 40
        label: 'Text to display'
        onchange: ->
          data.text = @value()
          return
    if linkList
      linkListCtrl =
        type: 'listbox'
        label: 'Link list'
        values: buildListItems(linkList, ((item) ->
          item.value = editor.convertURL(item.value or item.url, 'href')
          return
        ), [ {
          text: 'None'
          value: ''
        } ])
        onselect: linkListChangeHandler
        value: editor.convertURL(data.href, 'href')
        onPostRender: ->

          ###eslint consistent-this:0###

          linkListCtrl = this
          return
    if editor.settings.target_list != false
      if !editor.settings.target_list
        editor.settings.target_list = [
          {
            text: 'None'
            value: ''
          }
          {
            text: 'New window'
            value: '_blank'
          }
        ]
      targetListCtrl =
        name: 'target'
        type: 'listbox'
        label: 'Target'
        values: buildListItems(editor.settings.target_list)
    if editor.settings.rel_list
      relListCtrl =
        name: 'rel'
        type: 'listbox'
        label: 'Rel'
        values: buildListItems(editor.settings.rel_list)
    if editor.settings.link_class_list
      classListCtrl =
        name: 'class'
        type: 'listbox'
        label: 'Class'
        values: buildListItems(editor.settings.link_class_list, (item) ->
          if item.value

            item.textStyle = ->
              editor.formatter.getCssText
                inline: 'a'
                classes: [ item.value ]

          return
        )
    if editor.settings.link_title != false
      linkTitleCtrl =
        name: 'title'
        type: 'textbox'
        label: 'Title'
        value: data.title
    win = editor.windowManager.open(
      title: 'Insert link'
      data: data
      body: [
        {
          name: 'href'
          type: 'filepicker'
          filetype: 'file'
          size: 40
          autofocus: true
          label: 'Url'
          onchange: urlChange
          onkeyup: updateText
        }
        textListCtrl
        linkTitleCtrl
        buildAnchorListControl(data.href)
        linkListCtrl
        relListCtrl
        targetListCtrl
        classListCtrl
      ]
      onSubmit: (e) ->

        ###eslint dot-notation: 0###

        href = undefined
        # Delay confirm since onSubmit will move focus

        delayedConfirm = (message, callback) ->
          rng = editor.selection.getRng()
          window.setTimeout (->
            editor.windowManager.confirm message, (state) ->
              editor.selection.setRng rng
              callback state
              return
            return
          ), 0
          return

        insertLink = ->
          linkAttrs =
            href: href
            target: if data.target then data.target else null
            rel: if data.rel then data.rel else null
            'class': if data['class'] then data['class'] else null
            title: if data.title then data.title else null
          if anchorElm
            editor.focus()
            if onlyText and data.text != initialText
              if 'innerText' of anchorElm
                anchorElm.innerText = data.text
              else
                anchorElm.textContent = data.text
            dom.setAttribs anchorElm, linkAttrs
            selection.select anchorElm
            editor.undoManager.add()
          else
            if onlyText
              editor.insertContent dom.createHTML('a', linkAttrs, dom.encode(data.text))
            else
              editor.execCommand 'mceInsertLink', false, linkAttrs
          return

        data = tinymce.extend(data, e.data)
        href = data.href
        if !href
          editor.execCommand 'unlink'
          return
        # Is email and not //user@domain.com
        if href.indexOf('@') > 0 and href.indexOf('//') == -1 and href.indexOf('mailto:') == -1
          delayedConfirm 'The URL you entered seems to be an email address. Do you want to add the required mailto: prefix?', (state) ->
            if state
              href = 'mailto:' + href
            insertLink()
            return
          return
        # Is not protocol prefixed
        if editor.settings.link_assume_external_targets and !/^\w+:/i.test(href) or !editor.settings.link_assume_external_targets and /^\s*www[\.|\d\.]/i.test(href)
          delayedConfirm 'The URL you entered seems to be an external link. Do you want to add the required http:// prefix?', (state) ->
            if state
              href = 'http://' + href
            insertLink()
            return
          return
        insertLink()
        return
    )
    return

  editor.addButton 'link',
    icon: 'link'
    tooltip: 'Insert/edit link'
    shortcut: 'Meta+K'
    onclick: createLinkList(showDialog)
    stateSelector: 'a[href]'
  editor.addButton 'unlink',
    icon: 'unlink'
    tooltip: 'Remove link'
    cmd: 'unlink'
    stateSelector: 'a[href]'
  editor.addShortcut 'Meta+K', '', createLinkList(showDialog)
  editor.addCommand 'mceLink', createLinkList(showDialog)
  @showDialog = showDialog
  editor.addMenuItem 'link',
    icon: 'link'
    text: 'Insert/edit link'
    shortcut: 'Meta+K'
    onclick: createLinkList(showDialog)
    stateSelector: 'a[href]'
    context: 'insert'
    prependToContext: true
  return
