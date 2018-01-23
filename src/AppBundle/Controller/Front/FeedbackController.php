<?php

namespace AppBundle\Controller\Front;

use AppBundle\Entity\Announce;
use AppBundle\Entity\Feedback;
use AppBundle\Entity\Loan;
use AppBundle\Entity\User;
use AppBundle\Service\Library;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Feedback controller.
 *
 * @Route("feedback")
 */
class FeedbackController extends Controller
{
    /**
     * Enter a feedback
     *
     * @Route("/", name="feedback_index")
     * @Method("GET")
     */
    public function indexAction(Request $request)
    {
        $currentUser = $this->get('security.token_storage')->getToken()->getUser();
        $status = 2;
        $em = $this->getDoctrine()->getManager();
        $myAnnounces = $em->getRepository(Announce::class)->findAnnouncesByOwner($currentUser, $status);
        $loansByOwner = $em->getRepository(Loan::class)->findLoansByOwner($currentUser, $status);
        $otherAnnounces = $em->getRepository(Announce::class)->findAnnouncesByApplicant($currentUser, $status);
        $loansByApplicant = $em->getRepository(Loan::class)->findLoansByApplicant($currentUser, $status);

        return $this->render('front/feedback/index.html.twig', array(
            'myAnnounces' => $myAnnounces,
            'loansByOwner' => $loansByOwner,
            'otherAnnounces' => $otherAnnounces,
            'loansByApplicant' => $loansByApplicant
        ));
    }

    /**
     * Enter a feedback
     *
     * @Route("/comment", name="feedback_comment")
     * @Method("POST")
     */
    public function commentAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $user = $request->get('user');
            $loan = $request->get('loan');
            $grade = $request->get('grade');
            $content = $request->get('content');
            $currentUser = $this->get('security.token_storage')->getToken()->getUser();

            if ($user != '' && $loan != '' && $grade != '' && $content != '') {
                $em = $this->getDoctrine()->getManager();

                $user = $em->getRepository(User::class)->find($user);

                if (!$user)
                    return new JsonResponse(array('type' => 'error', 'content' => 'Aucune utilisateur trouvé'));

                $loan = $em->getRepository(Loan::class)->find($loan);

                if (!$loan)
                    return new JsonResponse(array('type' => 'error', 'content' => 'Aucune demande trouvée'));

                $feedback = $em->getRepository(Feedback::class)->findOneBy(array('author' => $currentUser, 'target' => $user, 'loan' => $loan));

                if (!$feedback) {
                    $feedback = new Feedback();
                    $feedback->setAuthor($currentUser);
                    $feedback->setTarget($user);
                    $feedback->setLoan($loan);
                    $feedback->setGrade($grade);
                    $feedback->setComment($content);
                    $feedback->setPostDate(date('d/m/Y'));
                    $em->persist($feedback);
                    $em->flush();

                    $reverse = $em->getRepository(Feedback::class)->findOneBy(array('author' => $user, 'target' => $currentUser, 'loan' => $loan));

                    if ($reverse)  {
                        $loan->setStatus(3);
                        $em->persist($loan);
                    }

                    $em->flush();

                    return new JsonResponse(array('type' => 'success', 'content' => 'Commentaire posté'));
                }

                return new JsonResponse(array('type' => 'warning', 'content' => 'Commentaire déjà posté'));
            }

            return new JsonResponse(array('type' => 'error', 'content' => 'Données invalides'));
        }

        return new JsonResponse(array('type' => 'error', 'content' => 'Appel ajax uniquement'));
    }

    /**
     * Enter feedback applicant
     *
     * @Route("/applicant", name="feedback_applicant")
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
