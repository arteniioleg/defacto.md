<?php


namespace App\Controller;

use App\Entity\Status;
use App\Form\StatusType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(path="/admin/statuses")
 * @IsGranted("ROLE_ADMIN")
 */
class AdminStatusesController extends Controller
{
    /**
     * @Route(path="/add", name="admin_status_add")
     * @return Response
     */
    public function addAction(Request $request)
    {
        $form = $this->createForm(StatusType::class, null, []);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Status $status */
            $status = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $em->persist($status);
            $em->flush();

            $this->addFlash(
                'success',
                $this->get('translator')->trans('flash.status_created')
            );

            return $this->redirectToRoute('admin_promises');
        }

        return $this->render('admin/page/status/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route(path="/{id}", name="admin_status_edit")
     * @return Response
     */
    public function editAction(Request $request, string $id)
    {
        $status = $this->getDoctrine()->getRepository('App:Status')->find($id);
        if (!$status) {
            throw $this->createNotFoundException();
        }

        $form = $this->createForm(StatusType::class, $status, []);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Status $status */
            $status = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $em->persist($status);
            $em->flush();

            $this->addFlash(
                'success',
                $this->get('translator')->trans('flash.status_updated')
            );

            return $this->redirectToRoute('admin_promises');
        }

        return $this->render('admin/page/status/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}