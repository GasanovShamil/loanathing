<?php

namespace AppBundle\Controller\Front;

use AppBundle\Entity\Announce;
use AppBundle\Entity\Feedback;
use AppBundle\Entity\Loan;
use AppBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Feedback controller.
 *
 * @Route("feedback")
 */
class FeedbackController extends BaseController
{
    /**
     * Enter a feedback
     *
     * @Route("/", name="feedback_index")
     * @Method("GET")
     */
    public function indexAction(Request $request)
    {
        $status = 2;
        $em = $this->getDoctrine()->getManager();
        $notifications = $em->getRepository(Notification::class)->findNotificationsByUser($this->getCurrentUser());
        $myAnnounces = $em->getRepository(Announce::class)->findAnnouncesByOwner($this->getCurrentUser(), $status);
        $loansByOwner = $em->getRepository(Loan::class)->findLoansByOwner($this->getCurrentUser(), $status);
        $otherAnnounces = $em->getRepository(Announce::class)->findAnnouncesByApplicant($this->getCurrentUser(), $status);
        $loansByApplicant = $em->getRepository(Loan::class)->findLoansByApplicant($this->getCurrentUser(), $status);

        return $this->render('front/feedback/index.html.twig', array(
            'notifications' => $notifications,
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

            if ($user != '' && $loan != '' && $grade != '' && $content != '') {
                $em = $this->getDoctrine()->getManager();

                $user = $em->getRepository(User::class)->find($user);

                if (!$user)
                    return new JsonResponse(array('type' => 'error', 'content' => 'Aucune utilisateur trouvé'));

                $loan = $em->getRepository(Loan::class)->find($loan);

                if (!$loan)
                    return new JsonResponse(array('type' => 'error', 'content' => 'Aucune demande trouvée'));

                $feedback = $em->getRepository(Feedback::class)->findOneBy(array('author' => $this->getCurrentUser(), 'target' => $user, 'loan' => $loan));

                if (!$feedback) {
                    $feedback = new Feedback();
                    $feedback->setAuthor($this->getCurrentUser());
                    $feedback->setTarget($user);
                    $feedback->setLoan($loan);
                    $feedback->setGrade($grade);
                    $feedback->setComment($content);
                    $feedback->setPostDate(date('d/m/Y'));
                    $em->persist($feedback);

                    $reverse = $em->getRepository(Feedback::class)->findOneBy(array('author' => $user, 'target' => $this->getCurrentUser(), 'loan' => $loan));

                    if ($reverse)  {
                        $loan->setStatus(3);
                        $em->persist($loan);
                    }

                    $notification = new Notification();
                    $notification->setUser($user);
                    $notification->setContent('Commentaire posté');
                    $em->persist($notification);

                    $em->flush();

                    return new JsonResponse(array('type' => 'success', 'content' => 'Commentaire posté'));
                }

                return new JsonResponse(array('type' => 'warning', 'content' => 'Commentaire déjà posté'));
            }

            return new JsonResponse(array('type' => 'error', 'content' => 'Données invalides'));
        }

        return new JsonResponse(array('type' => 'error', 'content' => 'Appel ajax uniquement'));
    }
}
