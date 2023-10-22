<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Notifier\Notification\Notification;
use App\Entity\Club;
use App\Form\ClubType;

class ClubController extends AbstractController
{
    #[Route('/club', name: 'app_club')]
    public function index(): Response
    {
        return $this->render('club/index.html.twig', [
            'controller_name' => 'ClubController',
        ]);
    }
    #[Route('/clublist', name: 'app_clublist')]
    public function clubtList(Request $request,NotifierInterface $notifier): Response
    {
        $club = new Club();
        $entityManager = $this->getDoctrine()->getManager();
        $clubs = $entityManager->getRepository(Club::class)->findAll();
        $form = $this->createForm(ClubType::class, $club);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) { 
            // Set other properties
            $entityManager->persist($club);
            $entityManager->flush();
            $notifier->send(new Notification('club added', ['browser']));
            return $this->redirect('/clublist');
        }

        return $this->render('club/list.html.twig', [
            'clubs' => $clubs,'form' => $form->createView(),
        ]);
    } 
    #[Route('/club/edit/{id}', name:'club_edit')]
    public function editClub(Request $request,$id,NotifierInterface $notifier): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $club = $entityManager->getRepository(Club::class)->find($id);
        $name = $club->getREF();
        $form = $this->createForm(ClubType::class, $club);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $notifier->send(new Notification('Club is up to datet from '.$name.' to '.$club->getREF(), ['browser']));
            return $this->redirect('/clublist');
        }

        return $this->render('classroom/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    #[Route('/club/delete/{id}', name: 'club_delete')]
    public function delete($id,NotifierInterface $notifier): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $club = $entityManager->getRepository(Club::class)->find($id);
        $entityManager->remove($club);
        $entityManager->flush();
        $notifier->send(new Notification('student is deleted '.$club->getREF(), ['browser']));
        return $this->redirect('/clublist');
    }
}
