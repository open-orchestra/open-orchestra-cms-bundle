PO = PO or {}
PO.formPrototypes =
  addButtonContainer: "<li class=\"prototype-add-container\"></li>"
  addButton: "<button class=\"btn btn-success prototype-add\">__label__</button>"
  removeButton: "<button class=\"btn btn-warning prototype-remove\">__label__</button>"
  addPrototype: (collectionHolder) ->
    settings = {}
    prototype = collectionHolder.data("prototype")
    settings.limit = collectionHolder.data("limit")
    settings.required = collectionHolder.parent().prev().hasClass('required')
    settings.prototype = $(prototype.replace(/__name__label__/g, collectionHolder.data("prototype-label-new")))
    settings.prototype.find("input[required='required'][type='hidden']").addClass('focusable').attr('type', 'text')
    settings.addButton = $(@addButton.replace(/__label__/g, collectionHolder.data("prototype-label-add")))
    settings.removeButton = $(@removeButton.replace(/__label__/g, collectionHolder.data("prototype-label-remove")))
    settings.addButtonContainer = $(@addButtonContainer).append(settings.addButton)
    settings.callback = collectionHolder.data("prototype-callback-add")
    new PO.formPrototype(collectionHolder, settings)
    return

PO.formPrototype = (collectionHolder, settings) ->
  @collectionHolder = collectionHolder
  @settings = settings
  @addButtonExist = false
  self = this

  # add old class for know if this children is already save in database
  @collectionHolder.children().each () ->
    prototype = $(this)
    prototype = self.createRemoveButton($(this))
    if prototype.find(".alert-error").length is 0
      prototype.addClass("old").removeClass("new")
    else
      prototype.addClass "error"
    return

  @toogleAddButton()

  if @collectionHolder.children().length == 0 && @settings.required
    @addPrototype()

  return

PO.formPrototype:: =
  getIndex: ->
    return @collectionHolder.children('div').length

  toogleAddButton: ->
    if @settings.limit is `undefined` or @getIndex() < @settings.limit
      @createAddButton()
    else
      @removeAddButton()
    if @getIndex() < 2 && @settings.required
      $("button.prototype-remove", @collectionHolder).hide()
    else
      $("button.prototype-remove", @collectionHolder).show()
    return

  createAddButton: ->
    self = this
    unless @addButtonExist
      @collectionHolder.append @settings.addButtonContainer
      @addButtonExist = true
      # add event in add button
      @settings.addButton.on "click", (e) ->
        e.preventDefault()
        if self.settings.callback is undefined or eval(self.settings.callback)
          self.addPrototype()
        return
    return

  removeAddButton: ->
    if @addButtonExist
      @collectionHolder.children(".prototype-add-container").remove()
      @addButtonExist = false
    return

  createRemoveButton: (prototype) ->
    self = this
    newRemoveButton = self.settings.removeButton.clone()
    prototype.append newRemoveButton
    newRemoveButton.on "click", (e) ->
      e.preventDefault()
      self.removePrototype $(this)
      return

    prototype

  addPrototype: ->
    newPrototype = @settings.prototype.clone()
    newPrototype.html newPrototype.html().replace(/__name__/g, @getIndex())

    # Display the input in the page before the add button
    @settings.addButtonContainer.before newPrototype
    newPrototype.find("input").click()

    @createRemoveButton newPrototype
    # increase the index with one for the next item
    @toogleAddButton()
    return

  removePrototype: (removeButton) ->
    removeButton.parent().remove()
    @toogleAddButton()
    return
