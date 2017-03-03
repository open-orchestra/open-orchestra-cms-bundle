import AbstractBehavior from './AbstractBehavior'

/**
 * @class TagCondition
 */
class TagCondition extends AbstractBehavior
{
    isAndBooleanRegExp() {
        return new RegExp(/^(NOT ){0,1}([^ ]+?)( AND (NOT ){0,1}([^ ]+?))*$/);
    }
    
    isOrBooleanRegExp() {
        return new RegExp(/^(NOT ){0,1}([^ ]+?)( OR (NOT ){0,1}([^ ]+?))*$/);
    }
    
    getBalancedBracketsRegExp() {
        return new RegExp(/\( ([^\(\)]*) \)/);
    }
    
    operator() {
        return ['(', ')', 'AND', 'OR', 'NOT'];
    }

    /**
     * activate behavior
     *
     * @param {Object} $element - jQuery object
     */
    activate($element) {
        let tags = $element.data('tags');
        let prepopulatedTags = $element.val().split(' ');
        let context = this;
        $element.tokenInput(tags, {
            allowFreeTagging: $element.data('authorize-new'),
            onAdd: function(item) {
                context.applicateSqlCss($(this));
            },
            onDelete: function(item) {
                context.applicateSqlCss($(this));
            },
            propertyToSearch: 'text',
            theme: 'facebook',
            tokenFormatter: function(item) {
                item = _.findWhere(tags, {text: item.text}) || item;
                item.type = item.type || 'new';
                return '<li class="' + item.type + '">' + item.text + '</li>';
            },
            tokenDelimiter: ' ',
            zindex: 100002
        });
        for(let prepopulatedTag of prepopulatedTags) {
            prepopulatedTag = prepopulatedTag.trim();
            if (prepopulatedTag != '') {
                if (this.operator().indexOf(prepopulatedTag) != -1) {
                    $element.tokenInput('add', { 
                        id: prepopulatedTag,
                        text: prepopulatedTag,
                        type: 'operator'
                    });
                } else {
                    element.tokenInput('add', _.findWhere(tags, {id: prepopulatedTag}));
                }
            }
        }
        let ul = $('<ul class="operator-list">');
        for(let operator of this.operator()) {
            let click = (function (operator, $element){
                return function() {
                    $element.tokenInput('add', {
                        id: operator,
                        text: operator,
                        type: 'operator'
                    });
                    $('#token-input-' + $element.attr('id')).focus();
                }
            })(operator, $element);
            $('<li>').html(operator).on('click', click).appendTo(ul);
        }
        $element.parent().append(ul);
    }

    /**
     * deactivate behavior
     *
     * @param {Object} $element - jQuery object
     */
    deactivate($element) {
        $element.tokenInput('destroy');
    }

    applicateSqlCss(obj) {
        let addClass = 'operator';
        let removeClass = 'operator-ok';
        if (this.testSqlExpression(obj.val())) {
            addClass = 'operator-ok';
            removeClass = 'operator';
        }
        $('.token-input-list-facebook .operator, .token-input-list-facebook .operator-ok', obj.parent())
        .removeClass(removeClass).addClass(addClass);
    }
  
    testSqlExpression(testedString) {
        let isSqlExpression = true
        if(this.getBalancedBracketsRegExp().test(testedString)) {
            let subTestedString = this.getBalancedBracketsRegExp().exec(testedString);
            testedString = testedString.replace(subTestedString[0], '#');
            isSqlExpression = this.testSqlExpression(testedString) && (this.isAndBooleanRegExp().test(subTestedString[1]) || this.isOrBooleanRegExp().test(subTestedString[1]));
        } else {
            isSqlExpression = isSqlExpression && (this.isAndBooleanRegExp().test(testedString) || this.isOrBooleanRegExp().test(testedString));
        }
        
        return isSqlExpression
    }

    /**
     * return selector
     *
     * @return {String}
     */
    getSelector() {
        return '.select-boolean';
    }
}

export default (new TagCondition);
