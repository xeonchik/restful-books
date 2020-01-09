<?php

namespace App\Serializer;

use App\Entity\Contact;

/**
 * Class ContactSerializer
 * It is simple serializer without complex logic, but we could change fields format more flexible
 * @package PhoneBook\Serializer
 */
class ContactSerializer
{
    /**
     * @param Contact $contact
     * @return array
     */
    public function toArray(Contact $contact) : array
    {
        return [
            'id' => $contact->getId(),
            'first_name' => $contact->getFirstName(),
            'last_name' => $contact->getLastName(),
            'phone' => $contact->getPhone(),
            'country_code' => $contact->getCountryCode(),
            'timezone' => $contact->getTimeZone(),
            'updated_on' => $contact->getUpdatedOn()->format('c'),
            'inserted_on' => $contact->getInsertedOn()->format('c'),
        ];
    }

    /**
     * Hydrate object from array
     *
     * @param array $data
     * @param Contact|null $contact
     * @return Contact
     * @throws \Exception
     */
    public function fromArray(array $data, Contact $contact = null) : Contact
    {
        if ($contact === null) {
            $contact = new Contact();
        }

        isset($data['first_name']) ? $contact->setFirstName($data['first_name']) : null;
        isset($data['last_name']) ? $contact->setLastName($data['last_name']) : null;
        isset($data['phone']) ? $contact->setPhone($data['phone']) : null;
        isset($data['country_code']) ? $contact->setCountryCode($data['country_code']) : null;
        isset($data['timezone']) ? $contact->setTimeZone($data['timezone']) : null;
        // updated_on, inserted_on fields are not for modify

        return $contact;
    }
}
