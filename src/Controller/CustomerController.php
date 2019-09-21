<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Exception\ApiException;
use App\Form\CustomerType;
use App\Repository\CustomerRepository;
use App\Service\Serializer;
use App\Service\Workflow;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/customer", name="customer")
 */
class CustomerController extends AbstractController
{
    /**
     * @param Request $request
     * @return JsonResponse
     * @throws \Exception
     * @Route("", name="new", methods={"POST"})
     */
    public function new(
        Request $request
    )
    {
        $em = $this->getDoctrine()->getManager();
        $data = json_decode($request->getContent(), true);
        $customer = new Customer();
        $form = $this->createForm(CustomerType::class, $customer);
        $form->submit($data);
        $this->denyAccessUnlessGranted('create', $customer);
        $em->persist($customer);
        $em->flush();
        return new JsonResponse(
            $customer->getId(),
            JsonResponse::HTTP_OK
        );
    }

    /**
     * @param Serializer $serializer
     * @param Customer $customer
     * @param Request $request
     * @return JsonResponse
     * @throws \Exception
     * @Route("/{id}", name="edit", methods={"PUT"}, requirements={"id": "\d+"})
     */
    public function edit(
        Serializer $serializer,
        Customer $customer,
        Request $request
    )
    {
        $em = $this->getDoctrine()->getManager();
        $data = $serializer->deserialize($request->getContent());
        $form = $this->createForm(CustomerType::class, $customer);
        $form->submit($data);
        $this->denyAccessUnlessGranted('edit', $customer);
        $em->persist($customer);
        $em->flush();
        return new JsonResponse(
            $serializer->serialize($customer, ['customer', 'products']),
            JsonResponse::HTTP_OK
        );
    }

    /**
     * @param Customer $customer
     * @param string $transition
     * @param Serializer $serializer
     * @param Workflow $workflow
     * @return JsonResponse
     * @throws \Exception
     * @Route("/{id}/status", name="status", requirements={"id": "\d+", "transition": "\w+"})
     */
    public function status(
        Customer $customer,
        string $transition,
        Serializer $serializer,
        Workflow $workflow
    )
    {
        $customer = $workflow->transition($transition, $customer);
        return new JsonResponse(
            $serializer->serialize($customer, ['customer']),
            JsonResponse::HTTP_OK
        );
    }

    /**
     * @param Customer $customer
     * @return JsonResponse
     * @throws \Exception
     * @Route("/{id}", name="delete", methods={"DELETE"}, requirements={"id": "\d+"})
     */
    public function delete(
        Customer $customer
    )
    {
        $this->denyAccessUnlessGranted('delete', $customer);
        $em = $this->getDoctrine()->getManager();
        $em->remove($customer);
        $em->flush();
        return new JsonResponse(
            null,
            JsonResponse::HTTP_NO_CONTENT
        );
    }

    /**
     * @param Serializer $serializer
     * @param Customer $customer
     * @return JsonResponse
     * @throws \Exception
     * @Route("/{id}", name="view", methods={"GET"}, requirements={"id": "\d+"})
     */
    public function view(
        Serializer $serializer,
        Customer $customer
    )
    {
        $this->denyAccessUnlessGranted('view', $customer);
        return new JsonResponse(
            $serializer->serialize($customer, ['customer', 'products']),
            JsonResponse::HTTP_OK
        );
    }

    /**
     * @param Serializer $serializer
     * @param CustomerRepository $customerRepository
     * @param Request $request
     * @return JsonResponse
     * @throws \Exception
     * @Route("s", name="list", methods={"GET"})
     */
    public function list(
        Serializer $serializer,
        CustomerRepository $customerRepository,
        Request $request
    )
    {
        $customers = $customerRepository->findBy(
            [],
            $request->get('orderBy'),
            $request->get('limit'),
            $request->get('offset')
        );
        return new JsonResponse(
            $serializer->serialize($customers, ['customers', 'products']),
            JsonResponse::HTTP_OK
        );
    }
}