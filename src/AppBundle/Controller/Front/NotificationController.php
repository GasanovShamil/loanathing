<?php

namespace AppBundle\Controller\Front;

use AppBundle\Entity\Announce;
use AppBundle\Entity\Feedback;
use AppBundle\Entity\Loan;
use AppBundle\Entity\Notification;
use AppBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Notification controller.
 *
 * @Route("notification")
 */
class NotificationController extends BaseController
{
    /**
     * Enter a feedback
     *
     * @Route("/read", name="notification_read")
     * @Method("POST")
     */
    public function readAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $notification = $request->get('notification');

            if ($notification != '') {
                $em = $this->getDoctrine()->getManager();

                $notification = $em->getRepository(Notification::class)->findOneBy(array('id' => $notification, 'user' => $this->getCurrentUser()));

                if (!$notification)
                    return new JsonResponse(array('type' => 'error', 'content' => 'Aucune notification trouvée'));

                $notification->setIsNew(false);
                $em->persist($notification);
                $em->flush();

                return new JsonResponse(array('type' => 'success'));
            }

            return new JsonResponse(array('type' => 'error', 'content' => 'Données invalides'));
        }

        return new JsonResponse(array('type' => 'error', 'content' => 'Appel ajax uniquement'));
    }
}
