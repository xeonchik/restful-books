# RESTful Phone Book

This is a sample REST application made as a micro-framework app. This was inspired by Lumen and Silex.

Doctrine ORM integrated because it is well tested and stable object relation mapping system, that very helpful when we want to work with database as objects.

## Installation

Run the Vagrant script:

`vagrant up`

Add to the hosts:

`192.168.33.11   restful-phonebook.test`

You can use Doctrine CLI by command

`./vendor/bin/doctrine`

## REST API

`GET /api/contact/{id}`
Gets a single phone book item

`DELETE /api/contact/{id}`
Delete item from phone book

`PUT /api/contact/{id}`
Update item

`GET /api/contact-list`
Get a full list of contacts.
Or paginated list with query params `limit` and `offset` or `page`.

`POST /api/contact`
Create a contact. 

Post data example:
```
{
   "first_name": "Denis",
   "last_name": "Cool",
   "phone": "+1 541 7430122",
   "country_code": "RU",
   "timezone": "Europe/Moscow"
 }
```
