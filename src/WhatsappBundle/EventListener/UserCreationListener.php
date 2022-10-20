<?php

namespace WhatsappBundle\EventListener;

use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\Event\UserEvent;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Doctrine\ORM\EntityManager;

class UserCreationListener implements EventSubscriberInterface {

    protected $em;
    protected $user;

    public function __construct(EntityManager $em) {
        $this->em = $em;
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents() {
        return array(
            FOSUserEvents::REGISTRATION_SUCCESS => 'onRegistrationSuccess',
        );
    }

    public function onRegistrationSuccess(FormEvent $event) {
        $this->user = $event->getForm()->getData();
        $group_name = 'my_default_group_name';
        $entity = $this->em->getRepository('MoskitoUserBundle:Group')->findOneByName($group_name); // You could do that by Id, too
        $this->user->addGroup($entity);
        $this->em->flush();
    }
}
