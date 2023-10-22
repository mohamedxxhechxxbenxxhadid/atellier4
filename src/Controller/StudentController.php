<?php

namespace App\Controller;
use App\Entity\Student;
use App\Form\StudentType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\NotifierInterface;


class StudentController extends AbstractController
{
    #[Route('/student', name: 'app_student')]
    public function index(): Response
    {
        return $this->render('student/index.html.twig', [
            'controller_name' => 'StudentController',
        ]);
    }
    #[Route('/studentlist', name: 'app_studentllist')]
    public function studentList(Request $request,NotifierInterface $notifier): Response
    {
        $student = new Student();
        $entityManager = $this->getDoctrine()->getManager();
        $students = $entityManager->getRepository(Student::class)->findAll();
        $form = $this->createForm(StudentType::class, $student);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) { 
            // Set other properties
            $entityManager->persist($student);
            $entityManager->flush();
            $notifier->send(new Notification('student added', ['browser']));
            return $this->redirect('/studentlist');
        }

        return $this->render('student/list.html.twig', [
            'students' => $students,'form' => $form->createView(),
        ]);
    } 
    #[Route('/student/edit/{id}', name:'student_edit')]
    public function editStudent(Request $request,$id,NotifierInterface $notifier): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $student = $entityManager->getRepository(Student::class)->find($id);
        $name = $student->getName();
        $form = $this->createForm(StudentType::class, $student);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $notifier->send(new Notification('student is up to datet from '.$name.' to '.$student->getName(), ['browser']));
            return $this->redirect('/studentlist');
        }

        return $this->render('classroom/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    #[Route('/student/delete/{id}', name: 'student_delete')]
    public function delete(Request $request,$id,NotifierInterface $notifier): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $student = $entityManager->getRepository(Student::class)->find($id);
        $entityManager->remove($student);
        $entityManager->flush();
        $notifier->send(new Notification('student is deleted '.$student->getName(), ['browser']));
        return $this->redirect('/studentlist');
    }
    
}
