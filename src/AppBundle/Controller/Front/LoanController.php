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

            if ($id != '' && $start != '' && $end != '') {
                $em = $this->getDoctrine()->getManager();
                $announce = $em->getRepository(Announce::class)->find($id);

                if (!$announce)
                    return new JsonResponse(array('type' => 'error', 'content' => 'Aucune annonce trouvée'));

                if ($library->canApplyDates($announce, $start, $end)) {
                    $loan = new Loan();
                    $loan->setApplicant($this->get('security.token_storage')->getToken()->getUser());
                    $loan->setAnnounce($announce);
                    $loan->setStartDate($start);
                    $loan->setEndDate($end);
                    $em->persist($loan);
                    $em->flush();

                    return new JsonResponse(array('type' => 'success', 'content' => 'Votre demande est envoyée'));
                }

                return new JsonResponse(array('type' => 'error', 'content' => 'Dates incompatibles'));
            }

            return new JsonResponse(array('type' => 'error', 'content' => 'Données invalides'));
        }

        return new JsonResponse(array('type' => 'error', 'content' => 'Appel ajax uniquement'));
    }

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
            ->findLoansByOwner($this->get('security.token_storage')->getToken()->getUser(), 0);

        return $this->render('front/loan/answer.html.twig', array(
            'loans' => $loans
        ));
    }

    /**
     * Get one announce's information
     *
     * @Route("/answer/announce", name="loan_answer_announce")
     * @Method("POST")
     */
    public function announceAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $id = $request->get('id');

            if ($id != '') {
                $announce = $this->getDoctrine()->getManager()->getRepository(Announce::class)->find($id);

                if (!$announce)
                    return new JsonResponse(array('type' => 'error', 'content' => 'Aucune annonce trouvée'));

                return new JsonResponse(array('type' => 'success', 'content' => array(
                    'name' => $announce->getName(),
                    'dates' => 'Du '.$announce->getStartDate().' au '.$announce->getEndDate(),
                    'image' => $request->getBaseUrl().'/../../assets/img/announces/'.$announce->getImage(),
                    'description' => $announce->getDescription()
                )));
            }

            return new JsonResponse(array('type' => 'error', 'content' => 'Données invalides'));
        }

        return new JsonResponse(array('type' => 'error', 'content' => 'Appel ajax uniquement'));
    }

    /**
     * Get one user's information
     *
     * @Route("/answer/user", name="loan_answer_user")
     * @Method("POST")
     */
    public function userAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $id = $request->get('id');

            if ($id != '') {
                $user = $this->getDoctrine()->getManager()->getRepository(User::class)->find($id);

                if (!$user)
                    return new JsonResponse(array('type' => 'error', 'content' => 'Aucune utilisateur trouvé'));

                return new JsonResponse(array('type' => 'success', 'content' => array(
                    'username' => $user->getUsername()
                )));
            }

            return new JsonResponse(array('type' => 'error', 'content' => 'Données invalides'));
        }

        return new JsonResponse(array('type' => 'error', 'content' => 'Appel ajax uniquement'));
    }

    /**
     * Accept a loan
     *
     * @Route("/answer/accept", name="loan_answer_accept")
     * @Method("POST")
     */
    public function acceptAction(Request $request, Library $library)
    {
        if ($request->isXmlHttpRequest()) {
            $id = $request->get('id');

            if ($id != '') {
                $em = $this->getDoctrine()->getManager();
                $loan = $em->getRepository(Loan::class)->find($id);

                if (!$loan)
                    return new JsonResponse(array('type' => 'error', 'content' => 'Aucune demande trouvée'));

                $loan->setOwnerCode($library->generateCode());
                $loan->setApplicantCode($library->generateCode());
                $loan->setStatus(1);
                $em->persist($loan);
                $em->flush();

                return new JsonResponse(array('type' => 'success', 'content' => 'Demande acceptée'));

            }

            return new JsonResponse(array('type' => 'error', 'content' => 'Données invalides'));
        }

        return new JsonResponse(array('type' => 'error', 'content' => 'Appel ajax uniquement'));
    }

    /**
     * Refuse a loan
     *
     * @Route("/answer/refuse", name="loan_answer_refuse")
     * @Method("POST")
     */
    public function refuseAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $id = $request->get('id');

            if ($id != '') {
                $em = $this->getDoctrine()->getManager();
                $loan = $em->getRepository(Loan::class)->find($id);

                if (!$loan)
                    return new JsonResponse(array('type' => 'error', 'content' => 'Aucune demande trouvée'));

                $em->remove($loan);
                $em->flush();

                return new JsonResponse(array('type' => 'success', 'content' => 'Demande refusée'));

            }

            return new JsonResponse(array('type' => 'error', 'content' => 'Données invalides'));
        }

        return new JsonResponse(array('type' => 'error', 'content' => 'Appel ajax uniquement'));
    }

    /**
     * Enter a code
     *
     * @Route("/code", name="loan_code")
     * @Method("GET")
     */
    public function codeAction(Request $request)
    {
        $currentUser = $this->get('security.token_storage')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();
        $myAnnounces = $em->getRepository(Announce::class)->findAnnouncesByOwner($currentUser, 1);
        $loansByOwner = $em->getRepository(Loan::class)->findLoansByOwner($currentUser, 1);
        $otherAnnounces = $em->getRepository(Announce::class)->findAnnouncesByApplicant($currentUser, 1);
        $loansByApplicant = $em->getRepository(Loan::class)->findLoansByApplicant($currentUser, 1);

        return $this->render('front/loan/code.html.twig', array(
            'myAnnounces' => $myAnnounces,
            'loansByOwner' => $loansByOwner,
            'otherAnnounces' => $otherAnnounces,
            'loansByApplicant' => $loansByApplicant
        ));
    }

    /**
     * Enter code owner
     *
     * @Route("/code/owner", name="loan_code_owner")
     * @Method("POST")
     */
    public function ownerAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $id = $request->get('id');
            $code = $request->get('code');

            if ($id != '' && strlen($code) == 10) {
                $em = $this->getDoctrine()->getManager();
                $loan = $em->getRepository(Loan::class)->find($id);

                if (!$loan)
                    return new JsonResponse(array('type' => 'error', 'content' => 'Aucune demande trouvée'));

                if ($loan->getOwnerCode() == 'OK')
                    return new JsonResponse(array('type' => 'warning', 'content' => 'Code déja rentré'));

                if ($loan->getOwnerCode() != $code)
                    return new JsonResponse(array('type' => 'error', 'content' => 'Code faux'));

                $loan->setOwnerCode('OK');
                $em->persist($loan);
                $em->flush();

                return new JsonResponse(array('type' => 'success', 'content' => 'Code rentré'));
            }

            return new JsonResponse(array('type' => 'error', 'content' => 'Données invalides'));
        }

        return new JsonResponse(array('type' => 'error', 'content' => 'Appel ajax uniquement'));
    }

    /**
     * Enter code applicant
     *
     * @Route("/code/applicant", name="loan_code_applicant")
     * @Method("POST")
     */
    public function applicantAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $id = $request->get('id');
            $code = $request->get('code');

            if ($id != '' && strlen($code) == 10) {
                $em = $this->getDoctrine()->getManager();
                $loan = $em->getRepository(Loan::class)->find($id);

                if (!$loan)
                    return new JsonResponse(array('type' => 'error', 'content' => 'Aucune demande trouvée'));

                if ($loan->getApplicantCode() == 'OK')
                    return new JsonResponse(array('type' => 'warning', 'content' => 'Code déja rentré'));

                if ($loan->getApplicantCode() != $code)
                    return new JsonResponse(array('type' => 'error', 'content' => 'Code faux'));

                $loan->setApplicantCode('OK');
                $em->persist($loan);
                $em->flush();

                return new JsonResponse(array('type' => 'success', 'content' => 'Code rentré'));
            }

            return new JsonResponse(array('type' => 'error', 'content' => 'Données invalides'));
        }

        return new JsonResponse(array('type' => 'error', 'content' => 'Appel ajax uniquement'));
    }
}
