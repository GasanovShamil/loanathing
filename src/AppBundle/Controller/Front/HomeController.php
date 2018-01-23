<?php

namespace AppBundle\Controller\Front;

use AppBundle\Entity\Notification;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

class HomeController extends BaseController
{
    /**
     * @Route("/", name="frontHomepage")
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $notifications = $em->getRepository(Notification::class)->findNotificationsByUser($this->getCurrentUser());

        return $this->render('front/home/index.html.twig', array(
            'notifications' => $notifications
        ));
    }
}
