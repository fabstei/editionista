<?php

namespace Editionista\WebsiteBundle\Entity;

use FOS\UserBundle\Entity\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="user")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToMany(targetEntity="Edition", mappedBy="users")
     */
    private $editions;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @var string
     */
    private $homepage;

    /**
     * @ORM\Column(type="string", length=80, nullable=true)
     * @var string
     */
    private $gravatar;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @var string
     */
    private $githubId;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @var string
     */
    private $githubToken;

    public function __construct()
    {
        $this->editions = new ArrayCollection();
        $this->createdAt = new \DateTime();
        parent::__construct();
    }

    public function toArray()
    {
        return array(
            'name' => $this->getUsername(),
            'email' => $this->getEmail(),
        );
    }

    /**
     * Add editions
     *
     * @param \Editionista\WebsiteBundle\Entity\Edition $editions
     */
    public function addEditions(Edition $editions)
    {
        $this->editions[] = $editions;
    }

    /**
     * Get editions
     *
     * @return \Doctrine\Common\Collections\Collection $editions
     */
    public function getEditions()
    {
        return $this->editions;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set homepage
     *
     * @param string $homepage
     */
    public function setHomepage($homepage)
    {
        $this->homepage = $homepage;
    }

    /**
     * Get homepage
     *
     * @return string
     */
    public function getHomepage()
    {
        return $this->homepage;
    }

    /**
     * Set gravatar
     *
     * @param string $gravatar
     */
    public function setGravatar($gravatar)
    {
        $this->gravatar = $gravatar;
    }

    /**
     * Get gravatar
     *
     * @return string
     */
    public function getGravatar()
    {
        return $this->gravatar;
    }

    /**
     * Get githubId.
     *
     * @return string
     */
    public function getGithubId()
    {
        return $this->githubId;
    }

    /**
     * Set githubId.
     *
     * @param string $githubId
     */
    public function setGithubId($githubId)
    {
        $this->githubId = $githubId;
    }

    /**
     * Get githubId.
     *
     * @return string
     */
    public function getGithubToken()
    {
        return $this->githubToken;
    }

    /**
     * Set githubToken.
     *
     * @param string $githubToken
     */
    public function setGithubToken($githubToken)
    {
        $this->githubToken = $githubToken;
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Add editions
     *
     * @param \Editionista\WebsiteBundle\Entity\Edition $editions
     * @return User
     */
    public function addEdition(\Editionista\WebsiteBundle\Entity\Edition $editions)
    {
        $this->editions[] = $editions;
    
        return $this;
    }

    /**
     * Remove editions
     *
     * @param \Editionista\WebsiteBundle\Entity\Edition $editions
     */
    public function removeEdition(\Editionista\WebsiteBundle\Entity\Edition $editions)
    {
        $this->editions->removeElement($editions);
    }
}