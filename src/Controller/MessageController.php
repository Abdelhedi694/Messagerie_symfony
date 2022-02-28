<?php

namespace App\Controller;

use App\Entity\Message;
use App\Entity\Reply;
use App\Form\MessageType;
use App\Form\ReplyType;
use App\Repository\MessageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MessageController extends AbstractController
{
    #[Route('/message', name: 'message')]
    public function index(MessageRepository $repository, EntityManagerInterface $manager, Request $request): Response
    {
        $message = new Message();
        $formulaire = $this->createForm(MessageType::class, $message);
        $formulaire->handleRequest($request);
        if ($formulaire->isSubmitted() && $formulaire->isValid()){

            $message->setUser($this->getUser());
            $manager->persist($message);
            $manager->flush();
            $this->redirectToRoute('message');

        }

        return $this->renderForm('message/index.html.twig', [
            'messages' => $repository->findAll(),
            'formulaire'=>$formulaire
        ]);
    }

    /**
     * @Route("/message/{id}", name="show_message")
     */
    public function show(Message $message){

        $reply = new Reply();
        $formulaire = $this->createForm(ReplyType::class, $reply);

        return $this->renderForm('message/show.html.twig', ['message'=>$message, 'formulaire'=>$formulaire]);
    }

    /**
     * @Route("/message/delete/{id}", name="delete_message")
     */
    public function delete(Message $message=null, EntityManagerInterface $manager){


       if ($message){
           $manager->remove($message);
           $manager->flush();
       }

       return $this->redirectToRoute("message");

    }

    /**
     * @Route("/message/update/{id}", name="update_message")
     */
    public function update(Message $message=null, Request $request, EntityManagerInterface $manager){

        if (!$message){
            return $this->redirectToRoute("message");
        }


        $formulaire = $this->createForm(MessageType::class, $message);
        $formulaire->handleRequest($request);
        if ($formulaire->isSubmitted() && $formulaire->isValid()){

            $manager->persist($message);
            $manager->flush();
            return $this->redirectToRoute('show_message', ['id'=>$message->getId()]);

        }



        return $this->renderForm("message/update.html.twig", ["formulaire"=>$formulaire]);
    }
}
