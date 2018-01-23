<?php

namespace AppBundle\Controller\Front;

use AppBundle\Entity\Announce;
use AppBundle\Entity\Feedback;
use AppBundle\Entity\Loan;
use AppBundle\Entity\Notification;
use AppBundle\Entity\User;
use AppBundle\Service\Library;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Loan controller.
 *
 * @Route("loan")
 */
class LoanController extends BaseController
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
                    $loan->setApplicant($this->getCurrentUser());
                    $loan->setAnnounce($announce);
                    $loan->setStartDate($start);
                    $loan->setEndDate($end);
                    $em->persist($loan);

                    $notification = new Notification();
                    $notification->setUser($announce->getOwner());
                    $notification->setContent('Nouveau candidat');
                    $em->persist($notification);

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
        $em = $this->getDoctrine()->getManager();
        $notifications = $em->getRepository(Notification::class)->findNotificationsByUser($this->getCurrentUser());
        $loans = $em->getRepository(Loan::class)->findLoansByOwner($this->getCurrentUser(), 0);

        return $this->render('front/loan/answer.html.twig', array(
            'notifications' => $notifications,
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
            $announce = $request->get('announce');

            if ($announce != '') {
                $announce = $this->getDoctrine()->getManager()->getRepository(Announce::class)->find($announce);

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
            $user = $request->get('user');
            $em = $this->getDoctrine()->getManager();

            if ($user != '') {
                $user = $em->getRepository(User::class)->find($user);

                $average = $em->getRepository(Feedback::class)->findAverageGradeByUser($user);
                $feedbacks = $em->getRepository(Feedback::class)->findFeedbacksByUser($user);

                $feedbacksArray = array();

                foreach ($feedbacks as $feedback) {
                    $feedbacksArray[] = array(
                        'date' => $feedback->getPostDate(),
                        'author' => $feedback->getAuthor()->getUsername(),
                        'grade' => $feedback->getGrade(),
                        'comment' => $feedback->getComment(),
                    );
                }

                if (!$user)
                    return new JsonResponse(array('type' => 'error', 'content' => 'Aucune utilisateur trouvé'));

                return new JsonResponse(array('type' => 'success', 'content' => array(
                    'username' => $user->getUsername(),
                    'average' => $average,
                    'feedbacks' => $feedbacksArray
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
    public function acceptAction(Request $request, Library $library, \Swift_Mailer $mailer)
    {
        if ($request->isXmlHttpRequest()) {
            $loan = $request->get('loan');

            if ($loan != '') {
                $em = $this->getDoctrine()->getManager();
                $loan = $em->getRepository(Loan::class)->find($loan);

                if (!$loan)
                    return new JsonResponse(array('type' => 'error', 'content' => 'Aucune demande trouvée'));

                $loan->setOwnerCode($library->generateCode());
                $loan->setApplicantCode($library->generateCode());
                $loan->setStatus(1);
                $em->persist($loan);

                $notification = new Notification();
                $notification->setUser($loan->getApplicant());
                $notification->setContent('Demande acceptée');
                $em->persist($notification);

                $em->flush();

                $mailer->send(
                    (new \Swift_Message('Vous avez un match !'))
                        ->setFrom('loanathing@gmail.com')
                        ->setTo($loan->getAnnounce()->getOwner()->getEmail())
                        ->setBody(
                            $this->renderView('email/match.html.twig', array(
                                    'name' => $loan->getAnnounce()->getOwner()->getUsername(),
                                    'announce' => $loan->getAnnounce()->getName(),
                                    'match' => $loan->getApplicant()->getUsername(),
                                    'email' => $loan->getApplicant()->getEmail(),
                                    'code' => $loan->getOwnerCode()
                                )
                            ), 'text/html')
                );

                $mailer->send(
                    (new \Swift_Message('Vous avez un match !'))
                        ->setFrom('loanathing@gmail.com')
                        ->setTo($loan->getApplicant()->getEmail())
                        ->setBody(
                            $this->renderView('email/match.html.twig', array(
                                    'name' => $loan->getApplicant()->getUsername(),
                                    'announce' => $loan->getAnnounce()->getName(),
                                    'match' => $loan->getAnnounce()->getOwner()->getUsername(),
                                    'email' => $loan->getAnnounce()->getOwner()->getEmail(),
                                    'code' => $loan->getApplicantCode()
                                )
                            ), 'text/html')
                );

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
            $loan = $request->get('loan');

            if ($loan != '') {
                $em = $this->getDoctrine()->getManager();
                $loan = $em->getRepository(Loan::class)->find($loan);

                if (!$loan)
                    return new JsonResponse(array('type' => 'error', 'content' => 'Aucune demande trouvée'));

                $em->remove($loan);

                $notification = new Notification();
                $notification->setUser($loan->getApplicant());
                $notification->setContent('Demande refusée');
                $em->persist($notification);

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
        $status = 1;
        $em = $this->getDoctrine()->getManager();
        $notifications = $em->getRepository(Notification::class)->findNotificationsByUser($this->getCurrentUser());
        $myAnnounces = $em->getRepository(Announce::class)->findAnnouncesByOwner($this->getCurrentUser(), $status);
        $loansByOwner = $em->getRepository(Loan::class)->findLoansByOwner($this->getCurrentUser(), $status);
        $otherAnnounces = $em->getRepository(Announce::class)->findAnnouncesByApplicant($this->getCurrentUser(), $status);
        $loansByApplicant = $em->getRepository(Loan::class)->findLoansByApplicant($this->getCurrentUser(), $status);

        return $this->render('front/loan/code.html.twig', array(
            'notifications' => $notifications,
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
            $loan = $request->get('loan');
            $code = $request->get('code');

            if ($loan != '' && strlen($code) == 10) {
                $em = $this->getDoctrine()->getManager();
                $loan = $em->getRepository(Loan::class)->find($loan);

                if (!$loan)
                    return new JsonResponse(array('type' => 'error', 'content' => 'Aucune demande trouvée'));

                if ($loan->getOwnerCode() == 'OK')
                    return new JsonResponse(array('type' => 'warning', 'content' => 'Code déja rentré'));

                if ($loan->getOwnerCode() != $code)
                    return new JsonResponse(array('type' => 'error', 'content' => 'Code faux'));

                $loan->setOwnerCode('OK');
                $em->persist($loan);

                $notification = new Notification();
                $notification->setUser($loan->getApplicant());
                $notification->setContent('Code rentré');
                $em->persist($notification);

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
            $loan = $request->get('loan');
            $code = $request->get('code');

            if ($loan != '' && strlen($code) == 10) {
                $em = $this->getDoctrine()->getManager();
                $loan = $em->getRepository(Loan::class)->find($loan);

                if (!$loan)
                    return new JsonResponse(array('type' => 'error', 'content' => 'Aucune demande trouvée'));

                if ($loan->getApplicantCode() == 'OK')
                    return new JsonResponse(array('type' => 'warning', 'content' => 'Code déja rentré'));

                if ($loan->getApplicantCode() != $code)
                    return new JsonResponse(array('type' => 'error', 'content' => 'Code faux'));

                $loan->setApplicantCode('OK');
                $em->persist($loan);

                $notification = new Notification();
                $notification->setUser($loan->getAnnounce()->getOwner());
                $notification->setContent('Code rentré');
                $em->persist($notification);

                $em->flush();

                return new JsonResponse(array('type' => 'success', 'content' => 'Code rentré'));
            }

            return new JsonResponse(array('type' => 'error', 'content' => 'Données invalides'));
        }

        return new JsonResponse(array('type' => 'error', 'content' => 'Appel ajax uniquement'));
    }
}
