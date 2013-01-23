<?php

namespace Editionista\WebsiteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;

/**
 * @ORM\Entity
 * @ORM\Table(name="version")
 */
class Version
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
     * @ORM\OneToMany(targetEntity="Edition", mappedBy="version")
     */
    private $editions;

    public function __construct($name = null)
    {
        $this->name = $name;
        $this->editions = new ArrayCollection();

    }

    /**
     * @param \Doctrine\ORM\EntityManager $em
     * @param                             $name
     * @param bool                        $create
     *
     * @return mixed|Tag
     * @throws \Doctrine\ORM\NoResultException
     */
    public static function getByName(EntityManager $em, $name, $create = false)
    {
        try {
            $qb = $em->createQueryBuilder();
            $qb->select('t')
                ->from(__CLASS__, 't')
                ->where('t.name = ?1')
                ->setMaxResults(1)
                ->setParameter(1, $name);

            return $qb->getQuery()->getSingleResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            if ($create) {
                $version = new self($name);
                $em->persist($version);

                return $version;
            }
            throw $e;
        }
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

    /**
     * Add editions
     *
     * @param \Editionista\WebsiteBundle\Entity\Edition $editions
     * @return Version
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

    /**
     * Get editions
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getEditions()
    {
        return $this->editions;
    }
}