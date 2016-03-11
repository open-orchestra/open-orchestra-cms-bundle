###*
 * @namespace OpenOrchestra:FormBehavior
###
window.OpenOrchestra or= {}
window.OpenOrchestra.FormBehavior or= {}

###*
 * @class Select2
###
class OpenOrchestra.FormBehavior.Select2 extends OpenOrchestra.FormBehavior.AbstractFormBehavior

  ###*
   * activateBehaviorOnElements
   * @param {Array} elements
   * @param {Object} view
   * @param {Object} form
  ###
  activateBehaviorOnElements: (elements, view, form) ->
    for element in elements
      element = $(element)
      tags = elements.data('tags')
      url = elements.data('check')
      grantedNew = elements.data('authorize-new')
      elements.select2(
        tags: tags
        createSearchChoice: (term, data) ->
          return false if !grantedNew
          if $(data).filter(->
            @text.localeCompare(term) is 0
          ).length is 0
            id: term
            text: term
            isNew: true
        formatResult: (term) ->
          if term.isNew
            $.ajax
              type: 'GET'
              url: url
              data: 'term=' + encodeURIComponent(term.text)
              success: (response) ->
                term.text = response.term
            "<span class=\"label label-danger\">New</span> " + term.text
          else
            term.text
        formatSelection: (term, container) ->
          container.parent().addClass('bg-color-red').attr('style', 'border-color:#a90329!important') if term.isNew
          term.text
      )

jQuery ->
  OpenOrchestra.FormBehavior.formBehaviorLibrary.add(new OpenOrchestra.FormBehavior.Select2(".select2"))
