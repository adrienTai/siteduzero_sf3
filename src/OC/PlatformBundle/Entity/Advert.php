<?php

namespace OC\PlatformBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

use OC\PlatformBundle\Validator\Antiflood;

/**
 * @ORM\Table(name="oc_advert")
 * @ORM\Entity(repositoryClass="OC\PlatformBundle\Repository\AdvertRepository")
 * @ORM\HasLifecycleCallbacks()
 * @UniqueEntity(fields="title", message="Une annonce existe déjà avec ce titre.")
 */
class Advert
{
  /**
   * @var int
   *
   * @ORM\Column(name="id", type="integer")
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  private $id;

  /**
   * @var \DateTime
   *
   * @ORM\Column(name="date", type="datetime")
   * @Assert\DateTime()
   */
  private $date;

  /**
   * @var string
   *
   * @ORM\Column(name="title", type="string", length=255)
   * @Assert\Length(min=10)
   */
  private $title;

  /**
   * @var string
   *
   * @ORM\Column(name="author", type="string", length=255)
   * @Assert\Length(min=2)
   */
  private $author;

	/**
	* $var string
	* 
	* @ORM\Column(name="email", type="string", length=255)
	*/
	private $email;

  /**
   * @var string
   *
   * @ORM\Column(name="content", type="text")
   * @Assert\NotBlank()
   * @Antiflood()
   */
  private $content;

  /**
   * @ORM\Column(name="published", type="boolean")
   */
  private $published = true;

  /**
   * @ORM\OneToOne(targetEntity="OC\PlatformBundle\Entity\Image", cascade={"persist", "remove"})
   * @Assert\Valid()
   */
  private $image;

  /**
   * @ORM\ManyToMany(targetEntity="OC\PlatformBundle\Entity\Category", cascade={"persist"})
   * @ORM\JoinTable(name="oc_advert_category")
   * @Assert\Valid()
   */
  private $categories;

  /**
   * @ORM\OneToMany(targetEntity="OC\PlatformBundle\Entity\Application", mappedBy="advert")
   * @Assert\Valid()
   */
  private $applications; // Notez le « s », une annonce est liée à plusieurs candidatures

	/**
	* @ORM\Column(name="updated_at", type="datetime", nullable=true)
	*/
	private $updatedAt;
	
	/**
    * @ORM\Column(name="nb_applications", type="integer")
    */
	private $nbApplications = 0;
	
	/**
	* @Gedmo\Slug(fields={"title"})
	* @ORM\Column(name="slug", type="string", length=255, unique=true)
	*/
	private $slug;
	
	/**
    * @ORM\Column(name="ip", type="string", length=50, nullable=true)
    * @Assert\Ip
    */
	private $ip;
	
	
  public function __construct()
  {
    $this->date 		= new \DateTime();
    $this->categories   = new ArrayCollection();
    $this->applications = new ArrayCollection();
  }

  /**
   * @return int
   */
  public function getId()
  {
    return $this->id;
  }

  /**
   * @param \DateTime $date
   */
  public function setDate(\DateTime $date)
  {
  	//I cheat a little bit because I use datepicker, so when I add a date I have smth like 2016-12-25 00:00:00
  	//so I add what is missing insteed of installing datetimepicker
  	$d = new \DateTime();
  	$e = \DateTime::createFromFormat('Y-m-d H:i:s', $date->format('Y-m-d').' '.$d->format('H:i:s'));
    $this->date = $e;
  }

  /**
   * @return \DateTime
   */
  public function getDate()
  {
    return $this->date;
  }

  /**
   * @param string $title
   */
  public function setTitle($title)
  {
    $this->title = $title;
  }

  /**
   * @return string
   */
  public function getTitle()
  {
    return $this->title;
  }

  /**
   * @param string $author
   */
  public function setAuthor($author)
  {
    $this->author = $author;
  }

  /**
   * @return string
   */
  public function getAuthor()
  {
    return $this->author;
  }

  /**
   * @param string $content
   */
  public function setContent($content)
  {
    $this->content = $content;
  }

  /**
   * @return string
   */
  public function getContent()
  {
    return $this->content;
  }
  
    /**
     * Set email
     *
     * @param string $email
     *
     * @return Advert
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }
    
  /**
   * @param bool $published
   */
  public function setPublished($published)
  {
    $this->published = $published;
  }

  /**
   * @return bool
   */
  public function getPublished()
  {
    return $this->published;
  }

  public function setImage(Image $image = null)
  {
    $this->image = $image;
  }

  public function getImage()
  {
    return $this->image;
  }

  /**
   * @param Category $category
   */
  public function addCategory(Category $category)
  {
    $this->categories[] = $category;
  }

  /**
   * @param Category $category
   */
  public function removeCategory(Category $category)
  {
    $this->categories->removeElement($category);
  }

  /**
   * @return ArrayCollection
   */
  public function getCategories()
  {
    return $this->categories;
  }

  /**
   * @param Application $application
   */
  public function addApplication(Application $application)
  {
    $this->applications[] = $application;

    // On lie l'annonce à la candidature
    $application->setAdvert($this);
  }

  /**
   * @param Application $application
   */
  public function removeApplication(Application $application)
  {
    $this->applications->removeElement($application);
  }

  /**
   * @return \Doctrine\Common\Collections\Collection
   */
  public function getApplications()
  {
    return $this->applications;
  }
  

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     *
     * @return Advert
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
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
	* @ORM\PreUpdate
	*/
	public function updateDate(){
		$this->setUpdatedAt(new \Datetime());
	}

    /**
     * Set nbApplications
     *
     * @param integer $nbApplications
     *
     * @return Advert
     */
    public function setNbApplications($nbApplications)
    {
        $this->nbApplications = $nbApplications;

        return $this;
    }

    /**
     * Get nbApplications
     *
     * @return integer
     */
    public function getNbApplications()
    {
        return $this->nbApplications;
    }
    
    public function increaseApplication()
    {
    	$this->nbApplications++;
    }
    public function decreaseApplication(){
    	$this->nbApplications--;
    }


    /**
     * Set slug
     *
     * @param string $slug
     *
     * @return Advert
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
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
	* @Assert\Callback
	*/
	public function isContentValid(ExecutionContextInterface $context)
	{
		$forbiddenWords = array('démotivation', 'abandon');

		// On vérifie que le contenu ne contient pas l'un des mots
		if (preg_match('#'.implode('|', $forbiddenWords).'#', $this->getContent())) {
		  // La règle est violée, on définit l'erreur
		  $context
			->buildViolation('Contenu invalide car il contient un mot interdit.') // message
			->atPath('content')                                                   // attribut de l'objet qui est violé
			->addViolation() // ceci déclenche l'erreur, ne l'oubliez pas
		  ;
		}
	}

    /**
     * Set ip
     *
     * @param string $ip
     *
     * @return Advert
     */
    public function setIp($ip)
    {
        $this->ip = $ip;

        return $this;
    }

    /**
     * Get ip
     *
     * @return string
     */
    public function getIp()
    {
        return $this->ip;
    }
    
    
}
