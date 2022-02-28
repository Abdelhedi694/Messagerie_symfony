<?php

namespace App\Controller;

use App\Entity\Message;
use App\Entity\Reply;
use App\Form\ReplyType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ReplyController extends AbstractController
{

    /**
     * @Route("/reply/new/{id}", name="new_reply")
     */
    public function new(Request $request, Message $message, EntityManagerInterface $manager){

        $reply = new Reply();
        $formulaire = $this->createForm(ReplyType::class, $reply);
        $formulaire->handleRequest($request);
        if ($formulaire->isSubmitted() && $formulaire->isValid()){

            $reply->setUser($this->getUser());
            $reply->setMessage($message);
            $manager->persist($reply);
            $manager->flush();
            return $this->redirectToRoute("show_message", ['id'=>$message->getId()]);

        }

    }

    #[Route('/reply/delete/{id}', name: 'delete_reply')]
    public function delete(Reply $reply, EntityManagerInterface $manager){

        if (!$reply){

            return $this->redirectToRoute('message');

        }
        $id = $reply->getMessage()->getId();
        $manager->remove($reply);
        $manager->flush();

        return $this->redirectToRoute("show_message", ['id'=>$id]);

    }

    #[Route('/reply/update/{id}', name: 'update_reply')]
    public function update(Reply $reply, Request $request, EntityManagerInterface $manager){

        $formulaire = $this->createForm(ReplyType::class, $reply);
        $formulaire->handleRequest($request);

        if ($formulaire->isSubmitted() && $formulaire->isValid()){

            $manager->persist($reply);
            $manager->flush();
            return $this->redirectToRoute('show_message', ['id'=>$reply->getMessage()->getId()]);

        }

        return $this->renderForm('reply/update.html.twig', ['formulaire'=>$formulaire]);
    }
}
