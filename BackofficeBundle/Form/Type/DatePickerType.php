<?php

namespace OpenOrchestra\BackofficeBundle\Form\Type;

use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

/**
 * Class DatePickerType
 */
class DatePickerType extends DateType
{

    /**
     * @param FormView $view
     * @param FormInterface $form
     * @param array $options
     */
    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['widget'] = $options['widget'];
        // Change the input to a HTML5 date input if
        //  * the widget is set to "single_text"
        //  * the format matches the one expected by HTML5
        //  * the html5 is set to true
        if ($options['html5'] && 'single_text' === $options['widget'] && self::HTML5_FORMAT === $options['format']) {
            $view->vars['type'] = 'date';
        }

        if($options['widget'] === 'single_text') {
            $view->vars['attr'] = array(
                'class' => 'datepicker',
                'data-dateformat' => $this->convertDateFormat($options['format'])
            );
        }

        if ($form->getConfig()->hasAttribute('formatter')) {
            $pattern = $form->getConfig()->getAttribute('formatter')->getPattern();

            // remove special characters unless the format was explicitly specified
            if (!is_string($options['format'])) {
                // remove quoted strings first
                $pattern = preg_replace('/\'[^\']+\'/', '', $pattern);

                // remove remaining special chars
                $pattern = preg_replace('/[^yMd]+/', '', $pattern);
            }

            // set right order with respect to locale (e.g.: de_DE=dd.MM.yy; en_US=M/d/yy)
            // lookup various formats at http://userguide.icu-project.org/formatparse/datetime
            if (preg_match('/^([yMd]+)[^yMd]*([yMd]+)[^yMd]*([yMd]+)$/', $pattern)) {
                $pattern = preg_replace(array('/y+/', '/M+/', '/d+/'), array('{{ year }}', '{{ month }}', '{{ day }}'), $pattern);
            } else {
                // default fallback
                $pattern = '{{ year }}{{ month }}{{ day }}';
            }

            $view->vars['date_pattern'] = $pattern;
        }
    }

    /**
     * Convert Symfony date format into jQuery date format
     *
     * @param $dateformat
     * @return string jQuery compatible date format
     */
    public function convertDateFormat($dateformat)
    {
        $patterns    = array('/MMMM/','/MMM/','/MM/','/M/','/yy/','/D/','/_m_/');
        $replacements = array('_m__m_','_m_','mm','m','y','o','M');

        return preg_replace($patterns, $replacements, $dateformat);
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'date_picker';
    }


}
