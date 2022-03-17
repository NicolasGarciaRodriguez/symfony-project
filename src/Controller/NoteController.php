<?php

namespace App\Controller;

use App\Form\NotaFormType;
use App\Form\FilterCategoryNoteFormType;
use App\Entity\Nota;
use App\Form\NotaType;
use Doctrine\Common\Collections\Expr\Value;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class NoteController extends AbstractController
{
    #[Route('nuevaNota', name: 'nuevaNota')]
    public function newNota(Request $request, EntityManagerInterface $em)
    {
        $form = $this->createForm(NotaType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $nota = $form->getData();
            $user = $this->getUser();
            $user->addNota($nota);
            $em->persist($nota);
            $em->flush();
            return $this->redirectToRoute('mynotes');
        }
        return $this->renderForm('/notas/createnota.html.twig', ['notaForm' => $form]);
    }

    #[Route('mynotes', name: 'mynotes')]
    public function listNotas(EntityManagerInterface $em)
    {
        $user = $this->getUser();
        $notas = $user->getNotas();
        return $this->render('/notas/misNotas.html.twig', ['notas' => $notas]);
    }

    #[Route('deletenota/{id}', name: 'deletenota')]
    public function deleteNote(EntityManagerInterface $em, Nota $notaABorrar)
    {
        $user = $this->getUser();
        $notas = $user->getNotas();
        foreach ($notas as $nota) {
            if ($nota === $notaABorrar) {
                $em->remove($notaABorrar);
                $em->flush();
                return $this->redirectToRoute("mynotes");
            }
        }
        return $this->render("/security/security.html.twig");
    }


    // #[Route("editnota/{id}", name: ("editnota"))]
    // public function editNota (EntityManagerInterface $em, Nota $notaAEditar)
    // {
    //     $user = $this->getUser();
    //     $notas = $user->getNotas();
    //     foreach ($notas as $nota) {
    //         if ($nota === $notaAEditar) {
    //             return $this->render("/notas/editnota.html.twig", ["notaAEditar" => $notaAEditar]);
    //         }
    //     }

    //     return $this->render("/security/security.html.twig");
    // }

    // #[Route("editnota/{id}/doedit", name: ("doedit"))]
    // public function doEdit(EntityManagerInterface $em, Nota $notaAEditar)
    // {
    //     $notaAEditar->setTexto("hola soy la nota editada");
    //     return $this->redirectToRoute("mynotes");
    // }
}
