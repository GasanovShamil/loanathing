<?php

namespace AppBundle\Controller\Front;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Base controller.
 */
class BaseController extends Controller
{
    protected function getCurrentUser()
    {
        return $this->get('security.token_storage')->getToken()->getUser();
    }
}
