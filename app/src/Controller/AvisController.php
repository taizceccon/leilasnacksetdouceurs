<?php

namespace App\Controller;

use App\Document\Avis;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Form\AvisType;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class AvisController extends AbstractController
{
    #[Route('/add-avis', name: 'app_add_avis')]
    public function add(Request $request, DocumentManager $dm): Response
    {
        $avis = new Avis();
        $form = $this->createForm(AvisType::class, $avis);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $dm->persist($avis);
            $dm->flush();

            $this->addFlash('success', 'Votre avis a bien été enregistré.');
            return $this->redirectToRoute('app_avis_list');
        }

        return $this->render('avis/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/avis', name: 'app_avis_list')]
    public function avis_list(DocumentManager $dm): Response
    {
        // Récupère tous les avis avec findAll
        $avisList = $dm->getRepository(Avis::class)->findAll();
     

        // Passe la liste des avis à la vue Twig
        return $this->render('avis/list.html.twig', [
            'avis' => $avisList,
        ]);
    }

    #[Route('/delete/{id}', name: 'app_avis_delete', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Request $request, DocumentManager $dm, string $id): RedirectResponse
    {
        $avis = $dm->getRepository(Avis::class)->find($id);

        if (!$avis) {
            $this->addFlash('error', 'Avis non trouvé.');
            return $this->redirectToRoute('app_avis_list');
        }

        if ($this->isCsrfTokenValid('delete-avis' . $avis->getId(), $request->request->get('_token'))) {
            $dm->remove($avis);
            $dm->flush();
            $this->addFlash('success', 'Avis supprimé avec succès.');
        } else {
            $this->addFlash('error', 'Jeton CSRF invalide.');
        }

        return $this->redirectToRoute('app_avis_list');
    }

    #[Route('/mongo-test', name: 'mongo_test')]
    public function test(DocumentManager $dm): Response
    {
        $avis = new Avis();
        $avis->setNom('Test')->setEmail('test@test.com')->setMessage('Ceci est un test');
        $dm->persist($avis);
        $dm->flush();

        return new Response('Avis sauvegardé');
    }

}
