<?php

namespace App\Controller;

use App\Entity\Categories;
use App\Form\CategoriesType;
use App\Entity\Tricks;
use App\Form\TricksType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;

class AdminController extends AbstractController
{
    /**
     * @Route("/Admin", name="Admin")
     * @Route("/tricks/{id}/edit", name="tricks_edit")
     */
    public function showadmin(Tricks $tricks = null, Request $request, ManagerRegistry $doctrine): Response
    {
        if ($this->getUser()) 
        {
            $user = $this->getUser();
            //Creat or edit Trick
            if (!$tricks) {
                $tricks = new Tricks();
                $tricks->setCreatedAt(new \DateTime());

                $tricks->setUsers($user);   
            } 
            
            $formtricks = $this->createForm(TricksType::class, $tricks);
            $formtricks->handleRequest($request);

            if($formtricks->isSubmitted() && $formtricks->isValid()) {

                if ($request->files->get('tricks')) {

                    // Traitement de l'image
                    $file = $formtricks->get('fitured_img')->getData();
                    $fichier = $file->getClientOriginalName();
    
                    // moves the file to the directory where images are stored
                    $file->move(
                        $this->getParameter('images_directory'),
                        $fichier
                    );
    
                    $tricks->setFituredImg($fichier);
                }
                
                $tricks->setModifyAt(new \DateTime());

                $em = $doctrine->getManager();
                $em->persist($tricks);
                $em->flush();


                return $this->redirectToRoute('tricks_show', [
                    'onglet' => 'profil',
                    'id' =>$tricks->getId(),
                ]);
            }

            //Creat category
            $repocat = $doctrine->getRepository(Categories::class);
            $cat = $repocat->findAll();
            $categorie = new Categories();

            $form = $this->createForm(CategoriesType::class, $categorie);

            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()) {
                
                $em = $doctrine->getManager();
                $em->persist($categorie);
                $em->flush();

                $onglet = 'categories';
                return $this->redirectToRoute('Admin', ['onglet' => $onglet]);

            }

            (!$tricks->getId())==null ? $onglet='tricks' : $onglet='profil';

            return $this->render('admin/index.html.twig', [
            'activee' => 'Admin',
            'formCategorie' => $form->createView(),
            'formTricks' => $formtricks->createView(),
            'Categories' => $cat,
            'onglet' => $onglet,
        ]);
        } else {
            return $this->redirectToRoute('app_home');
        }
    }
    /**
     * @Route("/mentions", name="mentions")
     */
    public function mentions(): Response
    {
        return $this->render('admin/mentions_legales.htmt.twig', [
            'activee' => 'Admin',
        ]);
    }
     
}
