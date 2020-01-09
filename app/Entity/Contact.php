<?php

namespace App\Entity;

/**
 * Class Contact
 * @package RestfulBooksApp\Entity
 *
 * @Entity
 * @Table(name="contacts")
 */
class Contact
{
    /**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue
     * @var int
     */
    protected $id;

    /**
     * @var string
     * @Column(type="string")
     */
    protected $firstName;

    /**
     * @var string
     * @Column(type="string", nullable=true)
     */
    protected $lastName;

    /**
     * @var string
     * @Column(type="string")
     */
    protected $phone;

    /**
     * @var string
     * @Column(type="string", nullable=true)
     */
    protected $countryCode;

    /**
     * @var string
     * @Column(type="string", nullable=true)
     */
    protected $timeZone;

    /**
     * @var \DateTime
     * @Column(type="datetime")
     */
    protected $insertedOn;

    /**
     * @var \DateTime
     * @Column(type="datetime")
     */
    protected $updatedOn;

    public function __construct()
    {
        $this->insertedOn = new \DateTime();
        $this->updatedOn = new \DateTime();
    }

    /**
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     */
    public function setFirstName(string $firstName): void
    {
        $this->firstName = $firstName;
    }

    /**
     * @return string
     */
    public function getLastName(): string
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     */
    public function setLastName(string $lastName): void
    {
        $this->lastName = $lastName;
    }

    /**
     * @return string
     */
    public function getPhone(): string
    {
        return $this->phone;
    }

    /**
     * @param string $phone
     */
    public function setPhone(string $phone): void
    {
        $this->phone = $phone;
    }

    /**
     * @return string
     */
    public function getCountryCode(): ?string
    {
        return $this->countryCode;
    }

    /**
     * @param string $countryCode
     */
    public function setCountryCode(?string $countryCode): void
    {
        $this->countryCode = $countryCode;
    }

    /**
     * @return string
     */
    public function getTimeZone(): ?string
    {
        return $this->timeZone;
    }

    /**
     * @param string $timeZone
     */
    public function setTimeZone(?string $timeZone): void
    {
        $this->timeZone = $timeZone;
    }

    /**
     * @return \DateTime
     */
    public function getInsertedOn(): \DateTime
    {
        return $this->insertedOn;
    }

    /**
     * @param \DateTime $insertedOn
     */
    public function setInsertedOn(\DateTime $insertedOn): void
    {
        $this->insertedOn = $insertedOn;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedOn(): \DateTime
    {
        return $this->updatedOn;
    }

    /**
     * @param \DateTime $updatedOn
     */
    public function setUpdatedOn(\DateTime $updatedOn): void
    {
        $this->updatedOn = $updatedOn;
    }
}
