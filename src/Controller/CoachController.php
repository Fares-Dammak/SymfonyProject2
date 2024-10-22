<?php

namespace App\Controller;

use App\Entity\Coach;
use App\Form\CoachType;
use App\Repository\CoachRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CoachController extends AbstractController
{
    #[Route('/coach', name: 'app_Coach')]
    public function index(): Response
    {
        return $this->render('coach/index.html.twig', [
            'controller_name' => 'CoachController',
        ]);
    }

    #[Route('/readC', name: 'readC')]
    public function readC(CoachRepository $rep): Response
    {
        $list = $rep->findAll();

        return $this->render('coach/afficheC.html.twig', [
            'coaches' => $list,
        ]);
    }

    #[Route('/add-coach', name: 'add_coach')]
    public function addCoach(Request $request, EntityManagerInterface $em): Response
    {
        $coach = new Coach(); // Create a new Coach entity
        $form = $this->createForm(CoachType::class, $coach); // Create the form
        $form->handleRequest($request); // Handle the form request
    
        if ($form->isSubmitted() && $form->isValid()) {
            // Check if CIN already exists
            $existingCoach = $em->getRepository(Coach::class)->findOneBy(['cin' => $coach->getCin()]);
            
            if ($existingCoach) {
                // If a coach with the same CIN exists, add a flash message
                $this->addFlash('error', 'CIN already exists!'); // Change 'success' to 'error'
                return $this->render('coach/add.html.twig', [
                    'f' => $form->createView(), // Render the form again
                ]);
            }
    
            // If CIN is unique, persist the new coach
            $em->persist($coach);
            $em->flush();
    
            $this->addFlash('success', 'Coach added successfully!');
            return $this->redirectToRoute('readC'); // Redirect after successful form submission
        }
    
        // Render the form for the first time or when there is an error
        return $this->render('coach/add.html.twig', [
            'f' => $form->createView(), // Pass the form view as 'f'
        ]);
    }
    

    #[Route('/add-coach-form', name: 'add_coach_form')]
    public function addCoachForm(): Response
    {
        return $this->render('coach/add.html.twig');
    }

    #[Route('/removeC/{id}', name: 'removeC')]
    public function removeC(EntityManagerInterface $em, $id, CoachRepository $rep): Response
    {
        $coach1 = $rep->find($id);

        if (!$coach1) {
            $this->addFlash('error', 'Coach not found.');
            return $this->redirectToRoute('readC');
        }

        $em->remove($coach1);
        $em->flush();

        $this->addFlash('success', 'Coach deleted successfully.');
        return $this->redirectToRoute('readC');
    }
}
