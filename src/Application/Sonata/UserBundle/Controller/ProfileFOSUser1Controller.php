<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Application\Sonata\UserBundle\Controller;

use FOS\UserBundle\Model\UserInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Sonata\UserBundle\Controller\ProfileFOSUser1Controller as Base;

/**
 * This class is inspired from the FOS Profile Controller, except :
 *   - only twig is supported
 *   - separation of the user authentication form with the profile form.
 */
class ProfileFOSUser1Controller extends Base
{
    /**
     * @return Response
     *
     * @throws AccessDeniedException
     */
    public function showAction()
    {
        $user = $this->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw $this->createAccessDeniedException('This user does not have access to this section.');
        }
        return $this->redirect($this->generateUrl('sonata_user_profile_edit'));
        return $this->render('SonataUserBundle:Profile:show.html.twig', array(
            'user' => $user,
            'blocks' => $this->container->getParameter('sonata.user.configuration.profile_blocks'),
        ));
    }

    /**
     * @return Response|RedirectResponse
     *
     * @throws AccessDeniedException
     */
    public function editAuthenticationAction()
    {
        return $this->redirectToRoute('fos_user_profile_edit');
        $user = $this->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw $this->createAccessDeniedException('This user does not have access to this section.');
        }

        $form = $this->get('sonata.user.authentication.form');
        $formHandler = $this->get('sonata.user.authentication.form_handler');

        $process = $formHandler->process($user);
        if ($process) {
            $this->setFlash('sonata_flash_success', 'Datos actualizados.');

            return $this->redirect($this->generateUrl('fos_user_profile_edit_authentication'));
        }

        return $this->render('SonataUserBundle:Profile:edit_authentication.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @return Response|RedirectResponse
     *
     * @throws AccessDeniedException
     */
    public function editProfileAction()
    {
        $user = $this->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw $this->createAccessDeniedException('This user does not have access to this section.');
        }

        $form = $this->get('sonata.user.profile.form');
        $formHandler = $this->get('sonata.user.profile.form.handler');

        $process = $formHandler->process($user);
        if ($process) {
            $this->setFlash('sonata_flash_success', 'Datos actualizados.');

            return $this->redirect($this->generateUrl('home'));
        }

        return $this->render('SonataUserBundle:Profile:edit_profile.html.twig', array(
            'form' => $form->createView(),
            'breadcrumb_context' => 'user_profile',
        ));
    }

    /**
     * @param string $action
     * @param string $value
     */
    protected function setFlash($action, $value)
    {
        $this->get('session')->getFlashBag()->set($action, $value);
    }
}
