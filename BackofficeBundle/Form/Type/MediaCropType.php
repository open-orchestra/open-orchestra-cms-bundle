<?php

namespace PHPOrchestra\BackofficeBundle\Form\Type;

use PHPOrchestra\BaseBundle\EventSubscriber\AddSubmitButtonSubscriber;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Translation\Translator;

/**
 * Class MediaCropType
 */
class MediaCropType extends AbstractType
{
    protected $thumbnailConfig;
    protected $translator;

    /**
     * @param array      $thumbnailConfig
     * @param Translator $translator
     */
    public function __construct(array $thumbnailConfig, Translator $translator)
    {
        $this->thumbnailConfig = $thumbnailConfig;
        $this->translator = $translator;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('x', 'hidden');
        $builder->add('y', 'hidden');
        $builder->add('h', 'hidden');
        $builder->add('w', 'hidden');
        $builder->add('format', 'choice', array(
            'choices' => $this->getChoices(),
            'label' => 'php_orchestra_backoffice.form.media.format',
            'required' => false,
        ));

        $builder->addEventSubscriber(new AddSubmitButtonSubscriber());
    }

    protected function getChoices()
    {
        $choices = array();

        foreach ($this->thumbnailConfig as $key => $thumbnail) {
            $choices[$key] = $this->translator->trans('php_orchestra_backoffice.form.media.' . $key);
        }

        return $choices;
    }
    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'media_crop';
    }
}
