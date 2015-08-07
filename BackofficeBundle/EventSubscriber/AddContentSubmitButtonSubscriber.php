<?php

namespace OpenOrchestra\BackofficeBundle\EventSubscriber;

use OpenOrchestra\ModelInterface\Model\StatusableInterface;
use OpenOrchestra\ModelInterface\Model\VersionableInterface;
use OpenOrchestra\ModelInterface\Repository\ContentRepositoryInterface;

class AddContentSubmitButtonSubscriber extends AddSubmitButtonSubscriber
{
    protected $contentRepository;

    /**
     * @param ContentRepositoryInterface $contentRepository
     */
    public function __construct(ContentRepositoryInterface $contentRepository)
    {
        $this->contentRepository = $contentRepository;
    }

    /**
     * @param $data
     *
     * @return array
     */
    protected function generateParameter($data)
    {
        $parameter = array('label' => 'open_orchestra_base.form.submit', 'attr' => array('class' => 'submit_form'));
        $isPublished = $data instanceof StatusableInterface && is_object($data->getStatus()) && $data->getStatus()->isPublished();
        $content = $this->contentRepository->findOneByContentIdAndLanguageAndVersion($data->getContentId(), $data->getLanguage(), null);
        $lastVersion = $content !== null ? $content->getVersion() : 1;
        $isLastVersion = $data instanceof VersionableInterface && $data->getVersion() >= $lastVersion;
        if ($isPublished || !$isLastVersion) {
            $parameter['attr'] = array('class' => 'disabled');
        }

        return $parameter;
    }
}
