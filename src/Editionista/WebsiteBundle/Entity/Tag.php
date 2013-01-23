<?php

namespace Editionista\WebsiteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;

/**
 * @ORM\Entity
 * @ORM\Table(name="tags")
 */
class Tag
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column
     * @Assert\NotBlank()
     */
    private $name;
    
    /**
     * @ORM\ManyToMany(targetEntity="Edition", mappedBy="tags", cascade={"persist"})
     */
    private $editions;


    public function __construct($name = null)
    {
        $this->name = $name;
        $this->editions = new ArrayCollection();
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    public function __toString()
    {
        return $this->name;
    }

    public function addEdition(Edition $edition)
    {
        $this->editions[] = $edition;
    }

    public function setEditions(array $editions)
    {
        foreach ($editions as $edition) {
            // first check if it does not contain it allready
            // because all entity objects are allready in memory
            // simply $em->find('Image', $id); and you will get it from this collection
            if (!$this->editions->contains($edition)) {
                $this->addEdition($edition);
            }
        }
    }

    public function getEditions()
    {
        return $this->editions;
    }
}