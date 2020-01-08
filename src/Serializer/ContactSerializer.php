<?php

namespace PhoneBook\Serializer;

use PhoneBook\Entity\Contact;

/**
 * Class ContactSerializer
 * It is simple serializer without complex logic, but we could change fields format more flexible
 * @package PhoneBook\Serializer
 */
class ContactSerializer
{
    public function toArray(Contact $contact) : array
    {
        return [
            'first_name' => $contact->getFirstName(),
            'last_name' => $contact->getLastName(),
            'phone' => $contact->getPhone(),
            'country_code' => $contact->getCountryCode(),
            'timezone' => $contact->getTimeZone(),
            'updated_on' => $contact->getUpdatedOn(),
            'inserted_on' => $contact->getInsertedOn(),
        ];
    }

    public function fromArray(array $data) : Contact
    {
        $contact = new Contact();

        isset($data['first_name']) ? $contact->setFirstName($data['first_name']) : null;
        isset($data['last_name']) ? $contact->setLastName($data['last_name']) : null;
        isset($data['phone']) ? $contact->setPhone($data['phone']) : null;
        isset($data['country_code']) ? $contact->setCountryCode($data['country_code']) : null;
        isset($data['timezone']) ? $contact->setTimeZone($data['timezone']) : null;
        isset($data['updated_on']) ? $contact->setUpdatedOn($data['updated_on']) : null;
        isset($data['inserted_on']) ? $contact->setInsertedOn($data['inserted_on']) : null;

        return $contact;
    }
}
