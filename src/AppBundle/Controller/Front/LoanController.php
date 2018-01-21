<?php

namespace AppBundle\Controller\Front;

use AppBundle\Entity\Announce;
use AppBundle\Entity\Loan;
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
     * Participate to an announce
     *
     * @Route("/answer", name="loan_answer")
     * @Method("GET")
     */
    public function answerAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $loans = $em->getRepository(Loan::class)->findLoansForAnnounces($this->get('security.token_storage')->getToken()->getUser()->getId());

        return $this->render('front/loan/answer.html.twig', array(
            'loans' => $loans,
        ));
    }

    /**
     * Participate to an announce
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
            }
        }
    }
}
