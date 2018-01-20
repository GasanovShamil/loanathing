<?php

namespace AppBundle\Controller\Front;

use AppBundle\Entity\Announce;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;

/**
 * Announce controller.
 *
 * @Route("announce")
 */
class AnnounceController extends Controller
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

        $announces = $em->getRepository('AppBundle:Announce')->findAll();

        return $this->render('front/announce/index.html.twig', array(
            'announces' => $announces,
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
        $announce = new Announce();
        $form = $this->createForm('AppBundle\Form\AnnounceType', $announce);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($announce);
            $em->flush();

            return $this->redirectToRoute('announce_show', array('id' => $announce->getId()));
        }

        return $this->render('front/announce/new.html.twig', array(
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
        $deleteForm = $this->createDeleteForm($announce);

        return $this->render('front/announce/show.html.twig', array(
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
        $deleteForm = $this->createDeleteForm($announce);
        $editForm = $this->createForm('AppBundle\Form\AnnounceType', $announce);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('announce_edit', array('id' => $announce->getId()));
        }

        return $this->render('front/announce/edit.html.twig', array(
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
