<?php

namespace App\Controller;
use App\Form\ClassroomType;
use App\Entity\Classroom;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\NotifierInterface;

class ClassroomController extends AbstractController
{
    #[Route('/classroom', name: 'app_classroom')]
    public function index(): Response
    {
        return $this->render('classroom/index.html.twig', [
            'controller_name' => 'ClassroomController',
        ]);
    }
    #[Route('/classroomlist', name: 'app_classroomllist')]
    public function ClassroomList(Request $request,NotifierInterface $notifier): Response
    {
        $classroom = new Classroom();
        $entityManager = $this->getDoctrine()->getManager();
        $classrooms = $entityManager->getRepository(Classroom::class)->findAll();
        $form = $this->createForm(ClassroomType::class, $classroom);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($classroom);
            $entityManager->flush();
            $notifier->send(new Notification('class added', ['browser']));
            return $this->redirect('/classroomlist');
        }

        return $this->render('classroom/list.html.twig', [
            'classrooms' => $classrooms,'form'=>$form->createView(),
        ]);
    }
    #[Route('/classroom/edit/{id}', name: 'classroom_edit')]
    public function edit(Request $request,$id,NotifierInterface $notifier): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $classroom = $entityManager->getRepository(Classroom::class)->find($id);
        $name = $classroom->getName();
        $form = $this->createForm(ClassroomType::class, $classroom);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $notifier->send(new Notification('class is up to datet from '.$name.' to '.$classroom->getName(), ['browser']));
            return $this->redirect('/classroomlist');
        }

        return $this->render('classroom/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    #[Route('/classroom/delete/{id}', name: 'classroom_delete')]
    public function delete(Request $request,$id,NotifierInterface $notifier): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $classroom = $entityManager->getRepository(Classroom::class)->find($id);
        $entityManager->remove($classroom);
        $entityManager->flush();
        $notifier->send(new Notification('class is deleted '.$classroom->getName(), ['browser']));
        return $this->redirect('/classroomlist');
    }
}
