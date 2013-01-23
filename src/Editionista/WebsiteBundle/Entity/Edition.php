<?php

namespace Editionista\WebsiteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity
 * @ORM\Table(name="edition")
 */
class Edition
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * Unique edition name
     *
     * @ORM\Column()
     */
    private $name;

    /**
     * @Gedmo\Slug(fields={"repository"}, style="default")
     * @ORM\Column(length=128, unique=true)
     */
    private $slug;
    
    /**
     * Repository
     *
     * @ORM\Column()
     */
    private $repository;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity="Version", inversedBy="editions")
     * @ORM\JoinColumn(name="version_id", referencedColumnName="id")
     */
    protected $version;
    
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $reference;

    
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $homepage;

    
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $readme;

    
    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $travis;

    
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $license;

    
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $licenseType;

    
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $packagist;

    /**
     * @ORM\ManyToMany(targetEntity="User", inversedBy="editions")
     * @ORM\JoinTable(name="users_editions")
     */
    private $users;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @ORM\ManyToMany(targetEntity="Tag", inversedBy="editions", cascade={"persist"})
     */
    private $tags;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedRepositoryAt;

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->tags = new ArrayCollection();
        $this->createdAt = new \DateTime;
    }

    /**
     * Get id
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
    
    /**
     * Get slug
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Get vendor prefix
     *
     * @return string
     */
    public function getVendor()
    {
        return preg_replace('{/.*$}', '', $this->repository);
    }

    /**
     * Get edition name without vendor
     *
     * @return string
     */
    public function getEditionName()
    {
        return preg_replace('{^[^/]*/}', '', $this->repository);
    }

    /**
     * Set description
     *
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
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
     * Set repository
     *
     * @param string $repository
     */
    public function setRepository($repository)
    {
        $this->repository = $repository;
    }

    /**
     * Get repository
     *
     * @return string $repository
     */
    public function getRepository()
    {
        return $this->repository;
    }

    /**
     * Add users
     *
     * @param \Packagist\WebBundle\Entity\User $user
     */
    public function addUser(User $user)
    {
        $this->users[] = $user;
    }

    /**
     * Get users
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * Remove users
     *
     * @param \Editionista\WebsiteBundle\Entity\User $users
     */
    public function removeUser(\Editionista\WebsiteBundle\Entity\User $users)
    {
        $this->users->removeElement($users);
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set reference
     *
     * @param string $reference
     * @return Edition
     */
    public function setReference($reference)
    {
        $this->reference = $reference;
    
        return $this;
    }

    /**
     * Get reference
     *
     * @return string 
     */
    public function getReference()
    {
        return $this->reference;
    }

    /**
     * Set homepage
     *
     * @param string $homepage
     * @return Edition
     */
    public function setHomepage($homepage)
    {
        $this->homepage = $homepage;
    
        return $this;
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
     * Set readme
     *
     * @param string $readme
     * @return Edition
     */
    public function setReadme($readme)
    {
        $this->readme = $readme;
    
        return $this;
    }

    /**
     * Get readme
     *
     * @return string 
     */
    public function getReadme()
    {
        return $this->readme;
    }

    /**
     * Set travis
     *
     * @param string $travis
     * @return Edition
     */
    public function setTravis($travis)
    {
        $this->travis = $travis;
    
        return $this;
    }

    /**
     * Get travis
     *
     * @return string 
     */
    public function getTravis()
    {
        return $this->travis;
    }

    /**
     * Set license
     *
     * @param string $license
     * @return Edition
     */
    public function setLicense($license)
    {
        $this->license = $license;
    
        return $this;
    }

    /**
     * Get license
     *
     * @return string 
     */
    public function getLicense()
    {
        return $this->license;
    }

    /**
     * Set licenseType
     *
     * @param string $licenseType
     * @return Edition
     */
    public function setLicenseType($licenseType)
    {
        $this->licenseType = $licenseType;
    
        return $this;
    }

    /**
     * Get licenseType
     *
     * @return string 
     */
    public function getLicenseType()
    {
        return $this->licenseType;
    }

    /**
     * Set packagist
     *
     * @param string $packagist
     * @return Edition
     */
    public function setPackagist($packagist)
    {
        $this->packagist = $packagist;
    
        return $this;
    }

    /**
     * Get packagist
     *
     * @return string 
     */
    public function getPackagist()
    {
        return $this->packagist;
    }

    /**
     * Set updatedRepositoryAt
     *
     * @param \DateTime $updatedRepositoryAt
     * @return Edition
     */
    public function setUpdatedRepositoryAt($updatedRepositoryAt)
    {
        $this->updatedRepositoryAt = $updatedRepositoryAt;
    
        return $this;
    }

    /**
     * Get updatedRepositoryAt
     *
     * @return \DateTime 
     */
    public function getUpdatedRepositoryAt()
    {
        return $this->updatedRepositoryAt;
    }

    /**
     * Set version
     *
     * @param \Editionista\WebsiteBundle\Entity\Version $version
     * @return Edition
     */
    public function setVersion(\Editionista\WebsiteBundle\Entity\Version $version = null)
    {
        $this->version = $version;
    
        return $this;
    }

    /**
     * Get version
     *
     * @return \Editionista\WebsiteBundle\Entity\Version 
     */
    public function getVersion()
    {
        return $this->version;
    }
 
    /**
     * Set slug
     *
     * @param string $slug
     * @return Edition
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
    
        return $this;
    }
    
    public function addTag(Tag $tag)
    {
        if(!$this->tags->contains($tag)) {
            $this->tags[] = $tag;
        }
    }

    public function setTags(array $tags)
    {
        $this->tags->clear();
        
        foreach($tags as $tag) {
            $this->addTag($tag);
        }
    }
    
    public function getTags()
    {
        return $this->tags;
    }
    
    /**
     * Remove users
     *
     * @param \Editionista\WebsiteBundle\Entity\Tag $tag
     */
    public function removeTag(\Editionista\WebsiteBundle\Entity\Tag $tag)
    {
        $this->tags->removeElement($tag);
    }

}