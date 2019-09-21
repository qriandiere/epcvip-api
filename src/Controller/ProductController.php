<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use App\Service\Serializer;
use App\Service\Workflow;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/product", name="product")
 */
class ProductController extends AbstractController
{
    /**
     * @param Serializer $serializer
     * @param Request $request
     * @return JsonResponse
     * @throws \Exception
     * @Route("", name="new", methods={"POST"})
     */
    public function new(
        Serializer $serializer,
        Request $request
    )
    {
        $em = $this->getDoctrine()->getManager();
        $data = $serializer->deserialize($request->getContent());
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->submit($data);
        $this->denyAccessUnlessGranted('create', $product);
        $em->persist($product);
        $em->flush();
        return new JsonResponse($product->getId(), JsonResponse::HTTP_OK);
    }

    /**
     * @param Serializer $serializer
     * @param Product $product
     * @param Request $request
     * @return JsonResponse
     * @throws \Exception
     * @Route("/{id}", name="edit", methods={"PUT"}, requirements={"id": "\d+"})
     */
    public function edit(
        Serializer $serializer,
        Product $product,
        Request $request
    )
    {
        $em = $this->getDoctrine()->getManager();
        $data = $serializer->deserialize($request->getContent());
        $form = $this->createForm(ProductType::class, $product);
        $form->submit($data);
        $this->denyAccessUnlessGranted('edit', $product);
        $em->persist($product);
        $em->flush();
        return new JsonResponse(
            $serializer->serialize($product, ['product', 'customer']),
            JsonResponse::HTTP_OK
        );
    }

    /**
     * @param Product $product
     * @param string $transition
     * @param Serializer $serializer
     * @param Workflow $workflow
     * @return JsonResponse
     * @throws \Exception
     * @Route("/{id}/status", name="status", requirements={"id": "\d+", "transition": "\w+"})
     */
    public function status(
        Product $product,
        string $transition,
        Serializer $serializer,
        Workflow $workflow
    )
    {
        $product = $workflow->transition($transition, $product);
        return new JsonResponse(
            $serializer->serialize($product, ['product']),
            JsonResponse::HTTP_OK
        );
    }

    /**
     * @param Product $product
     * @return JsonResponse
     * @throws \Exception
     * @Route("/{id}", name="delete", methods={"DELETE"}, requirements={"id": "\d+"})
     */
    public function delete(
        Product $product
    )
    {
        $this->denyAccessUnlessGranted('delete', $product);
        $em = $this->getDoctrine()->getManager();
        $em->remove($product);
        $em->flush();
        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }

    /**
     * @param Serializer $serializer
     * @param Product $product
     * @return JsonResponse
     * @throws \Exception
     * @Route("/{id}", name="view", methods={"GET"}, requirements={"id": "\d+"})
     */
    public function view(
        Serializer $serializer,
        Product $product
    )
    {
        $this->denyAccessUnlessGranted('view', $product);
        return new JsonResponse(
            $serializer->serialize($product, ['product', 'customer']),
            JsonResponse::HTTP_OK
        );
    }

    /**
     * @param Serializer $serializer
     * @param ProductRepository $productRepository
     * @param Request $request
     * @return JsonResponse
     * @throws \Exception
     * @Route("s", name="list", methods={"GET"})
     */
    public function list(
        Serializer $serializer,
        ProductRepository $productRepository,
        Request $request
    )
    {
        $products = $productRepository->findBy(
            [],
            $request->get('orderBy'),
            $request->get('limit'),
            $request->get('offset')
        );
        return new JsonResponse(
            $serializer->serialize($products, ['products', 'customer']),
            JsonResponse::HTTP_OK
        );
    }
}
