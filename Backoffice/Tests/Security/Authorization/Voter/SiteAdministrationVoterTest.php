<?php

namespace OpenOrchestra\Backoffice\Tests\Security\Authorization\Voter;

use OpenOrchestra\Backoffice\Security\ContributionActionInterface;
use OpenOrchestra\Backoffice\Security\ContributionRoleInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use OpenOrchestra\Backoffice\Security\Authorization\Voter\SiteAdministrationVoter;

/**
 * Class SiteAdministrationVoterTest
 */
class SiteAdministrationVoterTest extends AbstractVoterTest
{
    /**
     * Set up the test
     */
    public function setUp()
    {
        parent::setUp();

        $this->voter = new SiteAdministrationVoter($this->perimeterManager);
    }

    /**
     * @return array
     */
    protected function getNotSupportedSubjects()
    {
        $node = $this->createPhakeNode();
        $content = $this->createPhakeContent();
        $trashItem = $this->createPhakeTrashItem();
        $keyword = $this->createPhakeKeyword();
        $client = $this->createPhakeApiClient();
        $contentType = $this->createPhakeContentType();
        $profile = $this->createPhakeWorkflowProfile();
        $status = $this->createPhakeStatus();

        return array(
            'Bad subject : Node'             => array($node,        ContributionActionInterface::READ, array(ContributionRoleInterface::DEVELOPER), true, VoterInterface::ACCESS_ABSTAIN),
            'Bad subject : Content'          => array($content,     ContributionActionInterface::READ, array(ContributionRoleInterface::DEVELOPER), true, VoterInterface::ACCESS_ABSTAIN),
            'Bad subject : Trash Item'       => array($trashItem,   ContributionActionInterface::READ, array(ContributionRoleInterface::DEVELOPER), true, VoterInterface::ACCESS_ABSTAIN),
            'Bad subject : Keyword'          => array($keyword,     ContributionActionInterface::READ, array(ContributionRoleInterface::DEVELOPER), true, VoterInterface::ACCESS_ABSTAIN),
            'Bad subject : Api client'       => array($client,      ContributionActionInterface::READ, array(ContributionRoleInterface::DEVELOPER), true, VoterInterface::ACCESS_ABSTAIN),
            'Bad subject : Content type'     => array($contentType, ContributionActionInterface::READ, array(ContributionRoleInterface::DEVELOPER), true, VoterInterface::ACCESS_ABSTAIN),
            'Bad subject : Workflow profile' => array($profile,     ContributionActionInterface::READ, array(ContributionRoleInterface::DEVELOPER), true, VoterInterface::ACCESS_ABSTAIN),
            'Bad subject : Status'           => array($status,      ContributionActionInterface::READ, array(ContributionRoleInterface::DEVELOPER), true, VoterInterface::ACCESS_ABSTAIN),
        );
    }

    /**
     * @return array
     */
    protected function getNotSupportedAttributes()
    {
        $site = $this->createPhakeSite();

        return array(
            'Bad action : Trash Purge'   => array($site, ContributionActionInterface::TRASH_PURGE,   array(ContributionRoleInterface::DEVELOPER), true, VoterInterface::ACCESS_ABSTAIN),
            'Bad action : Trash Restore' => array($site, ContributionActionInterface::TRASH_RESTORE, array(ContributionRoleInterface::DEVELOPER), true, VoterInterface::ACCESS_ABSTAIN),
        );
    }

    /**
     * @return array
     */
    protected function getNotInPerimeter()
    {
        $site = $this->createPhakeSite();
        $redirection = $this->createPhakeRedirection();
        $user = $this->createPhakeUser();
        $group = $this->createPhakeGroup();

        return array(
            'Not in perimeter : Read site'          => array($site,        ContributionActionInterface::READ,   array(ContributionRoleInterface::SITE_ADMIN), false, VoterInterface::ACCESS_DENIED),
            'Not in perimeter : Edit site'          => array($site,        ContributionActionInterface::EDIT,   array(ContributionRoleInterface::SITE_ADMIN), false, VoterInterface::ACCESS_DENIED),
            'Not in perimeter : Delete site'        => array($site,        ContributionActionInterface::DELETE, array(ContributionRoleInterface::SITE_ADMIN), false, VoterInterface::ACCESS_DENIED),
            'Not in perimeter : Read redirection'   => array($redirection, ContributionActionInterface::READ,   array(ContributionRoleInterface::SITE_ADMIN), false, VoterInterface::ACCESS_DENIED),
            'Not in perimeter : Edit redirection'   => array($redirection, ContributionActionInterface::EDIT,   array(ContributionRoleInterface::SITE_ADMIN), false, VoterInterface::ACCESS_DENIED),
            'Not in perimeter : Delete redirection' => array($redirection, ContributionActionInterface::DELETE, array(ContributionRoleInterface::SITE_ADMIN), false, VoterInterface::ACCESS_DENIED),
            'Not in perimeter : Read user'          => array($user,        ContributionActionInterface::READ,   array(ContributionRoleInterface::SITE_ADMIN), false, VoterInterface::ACCESS_DENIED),
            'Not in perimeter : Edit user'          => array($user,        ContributionActionInterface::EDIT,   array(ContributionRoleInterface::SITE_ADMIN), false, VoterInterface::ACCESS_DENIED),
            'Not in perimeter : Delete user'        => array($user,        ContributionActionInterface::DELETE, array(ContributionRoleInterface::SITE_ADMIN), false, VoterInterface::ACCESS_DENIED),
            'Not in perimeter : Read group'         => array($group,       ContributionActionInterface::READ,   array(ContributionRoleInterface::SITE_ADMIN), false, VoterInterface::ACCESS_DENIED),
            'Not in perimeter : Edit group'         => array($group,       ContributionActionInterface::EDIT,   array(ContributionRoleInterface::SITE_ADMIN), false, VoterInterface::ACCESS_DENIED),
            'Not in perimeter : Delete group'       => array($group,       ContributionActionInterface::DELETE, array(ContributionRoleInterface::SITE_ADMIN), false, VoterInterface::ACCESS_DENIED),
        );
    }

    /**
     * @return array
     */
    protected function getBadRoles()
    {
        $actions = array(
            'Read'   => ContributionActionInterface::READ,
            'Edit'   => ContributionActionInterface::EDIT,
            'Delete' => ContributionActionInterface::DELETE,
        );

        $subjects = array(
            'site'        => $this->createPhakeSite(),
            'redirection' => $this->createPhakeRedirection(),
            'user'        => $this->createPhakeUser(),
            'group'       => $this->createPhakeGroup(),
        );

        $roles = array(
            'None' => array(),
            'Node contributor'        => array(ContributionRoleInterface::NODE_CONTRIBUTOR),
            'Node super editor'       => array(ContributionRoleInterface::NODE_SUPER_EDITOR),
            'Node super supressor'    => array(ContributionRoleInterface::NODE_SUPER_SUPRESSOR),
            'Content contributor'     => array(ContributionRoleInterface::CONTENT_CONTRIBUTOR),
            'Content super editor'    => array(ContributionRoleInterface::CONTENT_SUPER_EDITOR),
            'Content super supressor' => array(ContributionRoleInterface::CONTENT_SUPER_SUPRESSOR),
            'Trash restorer'          => array(ContributionRoleInterface::TRASH_RESTORER),
            'Trash supressor'         => array(ContributionRoleInterface::TRASH_SUPRESSOR),
        );

        $badRoles = array();

        foreach ($actions as $label => $action) {
            $key1 = 'Bad role (' . $label;

            foreach ($subjects as $label => $subject) {
                $key2 = ' ' . $label . ') : ';

                foreach ($roles as $label => $role) {
                    $badRoles[$key1 . $key2 . $label] = array($subject, $action, $role, true, VoterInterface::ACCESS_DENIED);
                }
            }
        }

        return $badRoles;
    }

    /**
     * @return array
     */
    protected function getOkVotes()
    {
        $log = $this->createPhakeLog();
        $site = $this->createPhakeSite();
        $redirection = $this->createPhakeRedirection();
        $user = $this->createPhakeUser();
        $group = $this->createPhakeGroup();

        return array(
            'Ok : Edit log'           => array($log,         ContributionActionInterface::EDIT,   array(ContributionRoleInterface::SITE_ADMIN), false, VoterInterface::ACCESS_GRANTED),
            'Ok : Delete log'         => array($log,         ContributionActionInterface::DELETE, array(ContributionRoleInterface::SITE_ADMIN), false, VoterInterface::ACCESS_GRANTED),
            'Ok : Edit site'          => array($site,        ContributionActionInterface::EDIT,   array(ContributionRoleInterface::SITE_ADMIN), true,  VoterInterface::ACCESS_GRANTED),
            'Ok : Delete site'        => array($site,        ContributionActionInterface::DELETE, array(ContributionRoleInterface::SITE_ADMIN), true,  VoterInterface::ACCESS_GRANTED),
            'Ok : Edit redirection'   => array($redirection, ContributionActionInterface::EDIT,   array(ContributionRoleInterface::SITE_ADMIN), true,  VoterInterface::ACCESS_GRANTED),
            'Ok : Delete redirection' => array($redirection, ContributionActionInterface::DELETE, array(ContributionRoleInterface::SITE_ADMIN), true,  VoterInterface::ACCESS_GRANTED),
            'Ok : Edit user'          => array($user,        ContributionActionInterface::EDIT,   array(ContributionRoleInterface::SITE_ADMIN), true,  VoterInterface::ACCESS_GRANTED),
            'Ok : Delete user'        => array($user,        ContributionActionInterface::DELETE, array(ContributionRoleInterface::SITE_ADMIN), true,  VoterInterface::ACCESS_GRANTED),
            'Ok : Edit group'         => array($group,       ContributionActionInterface::EDIT,   array(ContributionRoleInterface::SITE_ADMIN), true,  VoterInterface::ACCESS_GRANTED),
            'Ok : Delete group'       => array($group,       ContributionActionInterface::DELETE, array(ContributionRoleInterface::SITE_ADMIN), true,  VoterInterface::ACCESS_GRANTED),
        );
    }
}
