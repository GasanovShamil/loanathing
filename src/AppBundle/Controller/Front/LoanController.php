<?php

namespace AppBundle\Controller\Front;

use AppBundle\Entity\Announce;
use AppBundle\Entity\Loan;
use AppBundle\Entity\User;
use AppBundle\Service\Library;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Loan controller.
 *
 * @Route("loan")
 */
class LoanController extends Controller
{
    /**
     * Show list of my announces' loans
     *
     * @Route("/answer", name="loan_answer")
     * @Method("GET")
     */
    public function answerAction(Request $request)
    {
        $loans = $this->getDoctrine()
            ->getManager()
            ->getRepository(Loan::class)
            ->findLoansByOwner($this->get('security.token_storage')->getToken()->getUser());

        return $this->render('front/loan/answer.html.twig', array(
            'loans' => $loans,
        ));
    }

    /**
     * Apply to an announce
     *
     * @Route("/apply", name="loan_apply")
     * @Method("POST")
     */
    public function applyAction(Request $request, Library $library)
    {
        if ($request->isXmlHttpRequest()) {
            $id = $request->get('id');
            $start = $request->get('start');
            $end = $request->get('end');

            if ($id != "" && $start != "" && $end != "") {
                $em = $this->getDoctrine()->getManager();
                $announce = $em->getRepository(Announce::class)->find($id);

                if (!$announce) return new JsonResponse(array('type' => 'error', 'content' => 'Aucune annonce trouvée'));
                else if ($library->canApplyDates($announce, $start, $end)) {
                    $loan = new Loan();
                    $loan->setApplicant($this->get('security.token_storage')->getToken()->getUser());
                    $loan->setAnnounce($announce);
                    $loan->setStartDate($start);
                    $loan->setEndDate($end);
                    $em->persist($loan);
                    $em->flush();

                    return new JsonResponse(array('type' => 'success', 'content' => 'Votre demande est envoyée'));
                } else return new JsonResponse(array('type' => 'error', 'content' => 'Dates incompatibles'));
            } else return new JsonResponse(array('type' => 'error', 'content' => 'L\'id ne doit pas être vide'));
        } else return new JsonResponse(array('type' => 'error', 'content' => 'Appel ajax uniquement'));
    }

    /**
     * Get one announce's information
     *
     * @Route("/announce", name="loan_announce")
     * @Method("POST")
     */
    public function announceAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $id = $request->get('id');

            if ($id != '') {
                $announce = $this->getDoctrine()->getManager()->getRepository(Announce::class)->find($id);

                if (!$announce) return new JsonResponse(array('type' => 'error', 'content' => 'Aucune annonce trouvée'));
                else return new JsonResponse(array('type' => 'success', 'content' => array(
                    'name' => $announce->getName(),
                    'dates' => 'Du '.$announce->getStartDate().' au '.$announce->getEndDate(),
                    'image' => $request->getBaseUrl().'/../../assets/img/announces/'.$announce->getImage(),
                    'description' => $announce->getDescription()
                )));
            } else return new JsonResponse(array('type' => 'error', 'content' => 'L\'id ne doit pas être vide'));
        } else return new JsonResponse(array('type' => 'error', 'content' => 'Appel ajax uniquement'));
    }

    /**
     * Get one user's information
     *
     * @Route("/user", name="loan_user")
     * @Method("POST")
     */
    public function userAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $id = $request->get('id');

            if ($id != '') {
                $user = $this->getDoctrine()->getManager()->getRepository(User::class)->find($id);

                if (!$user) return new JsonResponse(array('type' => 'error', 'content' => 'Aucune utilisateur trouvé'));
                else return new JsonResponse(array('type' => 'success', 'content' => array(
                    'username' => $user->getUsername()
                )));
            } else return new JsonResponse(array('type' => 'error', 'content' => 'L\'id ne doit pas être vide'));
        } else return new JsonResponse(array('type' => 'error', 'content' => 'Appel ajax uniquement'));
    }

    /**
     * Accept a loan
     *
     * @Route("/accept", name="loan_accept")
     * @Method("POST")
     */
    public function acceptAction(Request $request, Library $library)
    {
        if ($request->isXmlHttpRequest()) {
            $id = $request->get('id');

            if ($id != "") {
                $em = $this->getDoctrine()->getManager();
                $loan = $em->getRepository(Loan::class)->find($id);

                if (!$loan) return new JsonResponse(array('type' => 'error', 'content' => 'Aucune demande trouvée'));

                $loan->setOwnerCode($library->generateCode());
                $loan->setApplicantCode($library->generateCode());
                $em->persist($loan);
                $em->flush();

                return new JsonResponse(array('type' => 'success', 'content' => 'Demande acceptée'));

            } else return new JsonResponse(array('type' => 'error', 'content' => 'L\'id ne doit pas être vide'));
        } else return new JsonResponse(array('type' => 'error', 'content' => 'Appel ajax uniquement'));
    }

    /**
     * Refuse a loan
     *
     * @Route("/refuse", name="loan_refuse")
     * @Method("POST")
     */
    public function refuseAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $id = $request->get('id');

            if ($id != "") {
                $em = $this->getDoctrine()->getManager();
                $loan = $em->getRepository(Loan::class)->find($id);

                if (!$loan) return new JsonResponse(array('type' => 'error', 'content' => 'Aucune demande trouvée'));

                $em->remove($loan);
                $em->flush();

                return new JsonResponse(array('type' => 'success', 'content' => 'Demande refusée'));

            } else return new JsonResponse(array('type' => 'error', 'content' => 'L\'id ne doit pas être vide'));
        } else return new JsonResponse(array('type' => 'error', 'content' => 'Appel ajax uniquement'));
    }

    /**
     * Enter a code
     *
     * @Route("/code", name="loan_code")
     * @Method("GET")
     */
    public function codeAction(Request $request)
    {
        $loans = $this->getDoctrine()
            ->getManager()
            ->getRepository(Loan::class)
            ->findLoansByOwner($this->get('security.token_storage')->getToken()->getUser());

        return $this->render('front/loan/answer.html.twig', array(
            'loans' => $loans,
        ));
    }
}
