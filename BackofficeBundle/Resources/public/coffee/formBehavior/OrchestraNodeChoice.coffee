###*
 * @namespace OpenOrchestra:FormBehavior
###
window.OpenOrchestra or= {}
window.OpenOrchestra.FormBehavior or= {}

###*
 * @class OrchestraNodeChoice
###
class OpenOrchestra.FormBehavior.OrchestraNodeChoice extends OpenOrchestra.FormBehavior.AbstractFormBehavior

  ###*
   * activateBehaviorOnElements
   * @param {Array} elements
   * @param {Object} view
   * @param {Object} form
  ###
  activateBehaviorOnElements: (elements, view, form) ->
    for element in elements
      element = $(element)
      regExp = new RegExp('((\u2502|\u251C|\u2514)+)', 'g')
      $('option', element).each ->
        $(this).addClass 'orchestra-node-choice'
        element.select2(
          allowClear: true,
          formatResult: (term) ->
            term.text.replace regExp, '<span class="hierarchical">$1</span>'
          formatSelection: (term) ->
            term.text.replace regExp, ''
        )

formBehaviorLibrary.add(new OpenOrchestra.FormBehavior.OrchestraNodeChoice(".orchestra-node-choice"))
