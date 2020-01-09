<?php

namespace App\Controller;

use Doctrine\ORM\EntityManager;
use Laminas\Diactoros\Response\JsonResponse;
use App\Entity\Contact;
use App\Exception\NotFoundException;
use App\Serializer\ContactSerializer;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class ContactApiController
 * RESTful API controller to manage phone book items.
 *
 * @package App\Controller
 * @author Maxim Tyuftin <xeonchik@gmail.com>
 */
class ContactApiController
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * ContactController constructor.
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * Gets a single phone book item
     *
     * @param ServerRequestInterface $request
     * @param $args
     * @return ResponseInterface
     * @throws \Exception
     */
    public function getItem(ServerRequestInterface $request, $args) : ResponseInterface
    {
        $id = (int)$args['id'];

        // we have a simple logic, therefore we don't need to make a services here
        $repository = $this->em->getRepository(Contact::class);
        /** @var Contact|null $contact */
        $contact = $repository->find($id);

        if (!$contact) {
            throw new NotFoundException("Contact (ID:$id) not found");
        }

        $serializer = new ContactSerializer();
        return new JsonResponse($serializer->toArray($contact), 200);
    }

    /**
     * Create a new contact item with a new ID
     *
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function createItem(ServerRequestInterface $request) : ResponseInterface
    {
        $data = json_decode($request->getBody()->getContents());

        $serializer = new ContactSerializer();
        $contact = $serializer->fromArray((array)$data);

        // TODO: validation
        // TODO: validation of country code, timezone

        $this->em->persist($contact);
        $this->em->flush();

        return new JsonResponse([], 201);
    }

    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function getList(ServerRequestInterface $request) : ResponseInterface
    {
        $byName = $request->getQueryParams()['name'] ?? null;
        $offset = $request->getQueryParams()['offset'] ?? null;
        $limit = $request->getQueryParams()['limit'] ?? null;
        $page = $request->getQueryParams()['page'] ?? null;

        $serializer = new ContactSerializer();
        $repository = $this->em->getRepository(Contact::class);
        $qb = $repository->createQueryBuilder('c');

        // filtering by contact name
        if ($byName) {
            $qb->where(
                $qb->expr()->orX(
                    $qb->expr()->like('c.firstName', $qb->expr()->literal('%' . $byName . '%')),
                    $qb->expr()->like('c.lastName', $qb->expr()->literal('%' . $byName . '%')),
                )
            );
        }

        if ($limit) {
            $qb->setMaxResults($limit);
        }

        if ($offset) {
            $qb->setFirstResult($offset);
        } else if ($page) {
            $offset = (abs($page) - 1) * $limit;
            $qb->setFirstResult($offset);
        }

        $list = $qb->getQuery()->execute();

        $result = [];
        foreach ($list as $item) {
            $result[] = $serializer->toArray($item);
        }

        return new JsonResponse($result, 200);
    }

    public function updateItem(ServerRequestInterface $request) : ResponseInterface
    {
        return new JsonResponse([], 200);
    }

    /**
     * @param ServerRequestInterface $request
     * @param array $args
     * @return ResponseInterface
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function deleteItem(ServerRequestInterface $request, array $args) : ResponseInterface
    {
        $id = (int)$args['id'];
        $repository = $this->em->getRepository(Contact::class);
        /** @var Contact|null $contact */
        $contact = $repository->find($id);

        if (!$contact) {
            throw new NotFoundException("Contact (ID:$id) not found");
        }

        $this->em->remove($contact);
        $this->em->flush();

        return new JsonResponse('', 200);
    }

    /**
     * Prepare error response object
     *
     * @param string $errorText
     * @param int $code
     * @return ResponseInterface
     */
    protected function errorResponse(string $errorText, int $code = 500) : ResponseInterface
    {
        return new JsonResponse([
            'success' => false,
            'error' => $errorText
        ], $code);
    }
}
