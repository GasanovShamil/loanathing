<?php

namespace AppBundle\Controller\Front;

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
     * @Route("/participate", name="loan_participate")
     * @Method("POST")
     */
    public function participateAction(Request $request, Library $library)
    {
        if ($request->isXmlHttpRequest()) {
            $id = $request->get('id');
            $start = $request->get('start');
            $end = $request->get('end');

            if ($id != "" && $start != "" && $end != "") {
                $em = $this->getDoctrine()->getManager();
                $announce = $em->getRepository('AppBundle:Announce')->find($id);

                if (!$announce) return new JsonResponse(array('type' => 'error', 'content' => 'Aucune annonce trouvée'));
                else if ($library->canApplyDates($announce, $start, $end)) {
                    return new JsonResponse(array('type' => 'success', 'content' => 'Votre demande est envoyée'));
                } else return new JsonResponse(array('type' => 'error', 'content' => 'Dates incompatibles'));
            }
        }
    }
}
