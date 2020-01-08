<?php

namespace PhoneBook\Controller;

use Doctrine\ORM\EntityManager;
use Laminas\Diactoros\Response\JsonResponse;
use PhoneBook\Serializer\ContactSerializer;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class ContactController
{
    /**
     * @var EntityManager
     */
    protected $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function index(ServerRequestInterface $request) : ResponseInterface
    {
        $response = new \Laminas\Diactoros\Response\JsonResponse([
            'test' => 1
        ]);
        return $response;
    }

    public function createItem(ServerRequestInterface $request) : ResponseInterface
    {
        $data = json_decode($request->getBody()->getContents());

        $serializer = new ContactSerializer();
        $contact = $serializer->fromArray((array)$data);

        // TODO: validation

        $this->em->persist($contact);
        $this->em->flush();

        return new JsonResponse([], 201);
    }
}
