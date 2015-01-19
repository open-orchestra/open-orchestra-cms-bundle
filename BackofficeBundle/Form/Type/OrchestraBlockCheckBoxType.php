<?php

namespace PHPOrchestra\BackofficeBundle\Form\Type;

use PHPOrchestra\BackofficeBundle\Form\DataTransformer\StringToBooleanTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class OrchestraBlockCheckBoxType
 */
class OrchestraBlockCheckBoxType extends AbstractType
{
    protected $StringToBooleanTransformer;

    /**
     * @param StringToBooleanTransformer $stringToBooleanTransformer
     */
    public function __construct(StringToBooleanTransformer $stringToBooleanTransformer)
    {
        $this->StringToBooleanTransformer = $stringToBooleanTransformer;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer($this->StringToBooleanTransformer);
    }

    /**
     * @return null|string|\Symfony\Component\Form\FormTypeInterface
     */
    public function getParent()
    {
        return 'checkbox';
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'orchestra_block_checkbox';
    }
}
