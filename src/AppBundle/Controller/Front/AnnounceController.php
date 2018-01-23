<?php

namespace AppBundle\Controller\Front;

use AppBundle\Entity\Announce;
use AppBundle\Entity\Notification;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Announce controller.
 *
 * @Route("announce")
 */
class AnnounceController extends BaseController
{
    /**
     * Lists all announce entities.
     *
     * @Route("/", name="announce_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $notifications = $em->getRepository(Notification::class)->findNotificationsByUser($this->getCurrentUser());
        $announces = $em->getRepository(Announce::class)->findAll();

        return $this->render('front/announce/index.html.twig', array(
            'notifications' => $notifications,
            'announces' => $announces
        ));
    }

    /**
     * Creates a new announce entity.
     *
     * @Route("/new", name="announce_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $notifications = $em->getRepository(Notification::class)->findNotificationsByUser($this->getCurrentUser());

        $announce = new Announce();
        $form = $this->createForm('AppBundle\Form\AnnounceType', $announce);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $announce->setOwner($this->get('security.token_storage')->getToken()->getUser());
            $em->persist($announce);
            $em->flush();

            return $this->redirectToRoute('announce_show', array('id' => $announce->getId()));
        }

        return $this->render('front/announce/new.html.twig', array(
            'notifications' => $notifications,
            'announce' => $announce,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a announce entity.
     *
     * @Route("/{id}", name="announce_show")
     * @Method("GET")
     */
    public function showAction(Announce $announce)
    {
        $em = $this->getDoctrine()->getManager();
        $notifications = $em->getRepository(Notification::class)->findNotificationsByUser($this->getCurrentUser());
        $deleteForm = $this->createDeleteForm($announce);

        return $this->render('front/announce/show.html.twig', array(
            'notifications' => $notifications,
            'announce' => $announce,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing announce entity.
     *
     * @Route("/{id}/edit", name="announce_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Announce $announce)
    {
        $em = $this->getDoctrine()->getManager();
        $notifications = $em->getRepository(Notification::class)->findNotificationsByUser($this->getCurrentUser());
        $deleteForm = $this->createDeleteForm($announce);
        $editForm = $this->createForm('AppBundle\Form\AnnounceType', $announce);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em->flush();

            return $this->redirectToRoute('announce_edit', array('id' => $announce->getId()));
        }

        return $this->render('front/announce/edit.html.twig', array(
            'notifications' => $notifications,
            'announce' => $announce,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a announce entity.
     *
     * @Route("/{id}", name="announce_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Announce $announce)
    {
        $form = $this->createDeleteForm($announce);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($announce);
            $em->flush();
        }

        return $this->redirectToRoute('announce_index');
    }

    /**
     * Creates a form to delete a announce entity.
     *
     * @param Announce $announce The announce entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Announce $announce)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('announce_delete', array('id' => $announce->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
