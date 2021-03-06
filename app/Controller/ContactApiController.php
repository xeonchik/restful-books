<?php

namespace App\Controller;

use App\Service\ReferenceService;
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
     * @var
     */
    protected $referenceService;

    /**
     * ContactApiController constructor.
     * @param EntityManager $em
     * @param ReferenceService $referenceService
     */
    public function __construct(EntityManager $em, ReferenceService $referenceService)
    {
        $this->em = $em;
        $this->referenceService = $referenceService;
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
        $validator = $this->createContactValidator((array)$data);
        $valid = $validator->validate();

        if (!$valid) {
            return new JsonResponse([
                'success' => false,
                'errors' => $validator->errors()
            ], 400);
        }

        $serializer = new ContactSerializer();
        $contact = $serializer->fromArray((array)$data);

        $this->em->persist($contact);
        $this->em->flush();

        return new JsonResponse([], 201);
    }

    /**
     * Get list of contacts (optional with pagination or offset)
     *
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

        $totalCount = $repository->count([]);

        // paginate if limit is set
        if ($limit) {
            $qb->setMaxResults($limit);

            if ($offset) {
                $qb->setFirstResult($offset);
            } else if ($page) {
                $offset = (abs($page) - 1) * $limit;
                $qb->setFirstResult($offset);
            }
        }

        $list = $qb->getQuery()->execute();

        $result = [
            'total' => $totalCount,
            'items' => []
        ];
        foreach ($list as $item) {
            $result['items'][] = $serializer->toArray($item);
        }

        return new JsonResponse($result, 200);
    }

    /**
     * Update contact item
     *
     * @param ServerRequestInterface $request
     * @param array $args
     * @return ResponseInterface
     * @throws NotFoundException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function updateItem(ServerRequestInterface $request, array $args) : ResponseInterface
    {
        $id = (int)$args['id'] ?? null;

        // we have a simple logic, therefore we don't need to make a services here
        $repository = $this->em->getRepository(Contact::class);
        /** @var Contact|null $contact */
        $contact = $repository->find($id);

        if (!$contact) {
            throw new NotFoundException("Contact (ID:$id) not found");
        }

        $data = json_decode($request->getBody()->getContents());
        $validator = $this->createContactValidator((array)$data, false);
        $valid = $validator->validate();

        if (!$valid) {
            return new JsonResponse([
                'success' => false,
                'errors' => $validator->errors()
            ], 400);
        }

        $serializer = new ContactSerializer();
        $serializer->fromArray((array)$data, $contact);

        $this->em->persist($contact);
        $this->em->flush();

        return new JsonResponse('', 200);
    }

    /**
     * Delete contact item
     *
     * @param ServerRequestInterface $request
     * @param array $args
     * @return ResponseInterface
     * @throws NotFoundException
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

    /**
     * @param array $data
     * @param bool $checkRequired
     * @return \Valitron\Validator
     */
    protected function createContactValidator(array $data, $checkRequired = true) : \Valitron\Validator
    {
        $validator = new \Valitron\Validator($data);

        // Validate required fields if create record
        if ($checkRequired) {
            $validator->rule('required', [
                'first_name',
                'phone'
            ]);
        }

        $validator->rule('lengthMax', ['first_name', 'last_name'], 40);
        $validator->rule('regex', 'phone', '/^\+[0-9]{1,3} [0-9]{2,3} [0-9]{7}$/')
            ->message('Phone number is invalid (valid format is +1 123 1234567)');

        $validator->rule(function ($field, $value) {
            $list = $this->referenceService->getCountries();
            return isset($list[$value]);
        }, 'country_code')->message('Country code is not valid (see https://api.hostaway.com/countries)');

        $validator->rule(function ($field, $value) {
            $list = $this->referenceService->getTimezones();
            return isset($list[$value]);
        }, 'timezone')->message('Timezone is not valid (see https://api.hostaway.com/timezones)');

        return $validator;
    }
}
