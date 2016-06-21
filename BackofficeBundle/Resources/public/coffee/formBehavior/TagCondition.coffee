###*
 * @namespace OpenOrchestra:FormBehavior
###
window.OpenOrchestra or= {}
window.OpenOrchestra.FormBehavior or= {}

###*
 * @class TagCondition
###
class OpenOrchestra.FormBehavior.TagCondition extends OpenOrchestra.FormBehavior.AbstractFormBehavior

  isAndBooleanRegExp: new RegExp(/^((NOT (?=.)){0,1}[^ \(\)]+( AND (?=.)){0,1})+$/)
  isOrBooleanRegExp: new RegExp(/^((NOT (?=.)){0,1}[^ \(\)]+( OR (?=.)){0,1})+$/)
  getBalancedBracketsRegExp: new RegExp(/\( ([^\(\)]*) \)/)
  operator: ['(', ')', 'AND', 'OR', 'NOT']

  ###*
   * activateBehaviorOnElements
   * @param {Array} elements
   * @param {Object} view
   * @param {Object} form
  ###
  activateBehaviorOnElements: (elements, view, form) ->
    for element in elements
      element = $(element)
      tags = element.data('tags')
      prepopulatedTags = element.val().split(' ')
      context = @
      element.tokenInput tags,
        allowFreeTagging: element.data('authorize-new')
        onAdd: (item) ->
          context.applicateSqlCss $(this)
          return
        onDelete: (item) ->
          context.applicateSqlCss $(this)
          return
        propertyToSearch: 'text'
        theme: 'facebook'
        tokenFormatter: (item) ->
          item = _.findWhere(tags, text: item.text) or item
          item.type = item.type or 'new'
          '<li class="' + item.type + '">' + item.text + '</li>'
        tokenDelimiter: ' '
        zindex: 100002
      for i of prepopulatedTags
        prepopulatedTags[i] = prepopulatedTags[i].trim()
        if prepopulatedTags[i] != ''
          if @operator.indexOf(prepopulatedTags[i]) != -1
            element.tokenInput 'add', 
              id: prepopulatedTags[i]
              text: prepopulatedTags[i]
              type: 'operator'
          else
            element.tokenInput 'add', _.findWhere(tags,
              id: prepopulatedTags[i]
            )
      ul = $('<ul class="operator-list">')
      for i of @operator
        click = ((operator, element) ->
          ->
            element.tokenInput 'add',
              id: operator
              text: operator
              type: 'operator'
            $('#token-input-' + element.attr('id')).focus()
            return
        )(@operator[i], element)
        $('<li>').html(@operator[i]).on('click', click).appendTo ul
      element.parent().append ul

  ###*
   * applicateSqlCss
   * @param {Object} obj
  ###
  applicateSqlCss: (obj) ->
    isSqlExpression = @testSqlExpression 
      text: obj.val()
    addClass = if isSqlExpression then 'operator-ok' else 'operator'
    removeClass = if isSqlExpression then 'operator' else 'operator-ok'
    $('.token-input-list-facebook .operator, .token-input-list-facebook .operator-ok', obj.parent())
    .removeClass(removeClass).addClass addClass
  
  ###*
   * testSqlExpression
   * @param {String} testedString
  ###
  testSqlExpression: (testedString) ->
    isSqlExpression = true
    if @getBalancedBracketsRegExp.test(testedString.text)
      subTestedString = @getBalancedBracketsRegExp.exec(testedString.text)
      testedString.text = testedString.text.replace(subTestedString[0], '#')
      isSqlExpression = @testSqlExpression(testedString) and (@isAndBooleanRegExp.test(subTestedString[1]) or @isOrBooleanRegExp.test(subTestedString[1]))
    else
      isSqlExpression = isSqlExpression and (@isAndBooleanRegExp.test(testedString.text) or @isOrBooleanRegExp.test(testedString.text))
    return isSqlExpression

jQuery ->
  OpenOrchestra.FormBehavior.formBehaviorLibrary.add(new OpenOrchestra.FormBehavior.TagCondition(".select-boolean"))
