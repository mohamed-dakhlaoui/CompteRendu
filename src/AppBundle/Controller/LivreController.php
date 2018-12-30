<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\View\View;
use AppBundle\Entity\Livre;
use AppBundle\Entity\Auteur;

class LivreController extends FOSRestController
{
    /**
     * @Rest\Get("/books")
    */
    public function ListeLivre(Request $request)
    {
        $titre = $request->query->get('title');
        $auteur = $request->query->get('author');
        $em = $this->getDoctrine()->getManager();
        $Nom = $em->getRepository(Auteur::class)->findByNom($auteur);
             //Tous les Livres dont le titre contient une chaine passee en parametre.         
        if($titre){
        $Livre = $this->getDoctrine()->getRepository(Livre::class)->findByTitre($titre);
        }
            // Tous les Livres d'un auteur donné
        elseif($Nom){
            $Livre = $this->getDoctrine()->getRepository(Livre::class)->findByAuteur($Nom);
        }
             // tous les livres
        else{
            $Livre = $this->getDoctrine()->getRepository(Livre::class)->findAll();
        }
        if ($Livre === null){
        return new View("there are no Books", Response::HTTP_NOT_FOUND);
        }
        return $Livre;
    }
    //Detail d'un livre donnée
    /**
    * @Rest\Get("/books/{id}")
    */
    public function DetaitLivre($id)
    {
    $result = $this->getDoctrine()->getRepository(Livre::class)->find($id);
    if ($result === null)
    return new View("there are no Books for this ID", Response::HTTP_NOT_FOUND);
    return $result;
    }

    //Ajouter un nouveau livre
    /**
    * @Rest\Post("/books")
    */
    public function addLivre(Request $request)
    {
    $Livre = new Livre();
    $titre = $request->get('titre');
    $descriptif = $request->get('descriptif');
    $ISBN = $request->get('ISBN');
    $id_auteur = $request->get('id_auteur');
    $em = $this->getDoctrine()->getManager();
    $auteur = $em->getRepository(Auteur::class)->find($id_auteur);
      
    if(empty($titre) && empty ($descriptif)  && empty ($ISBN)  )
    {
    return new View("verifier le remplissage de Titre , descriptif , ISBN ", Response::HTTP_NOT_ACCEPTABLE);
    }
    if(!$auteur){
        return new View("pas d'auteur avec cette id", Response::HTTP_NOT_FOUND);
    }
    else{
        $Livre->setAuteur($auteur);
    }
    $Livre->setTitre($titre);
    $Livre->setDescriptif($descriptif);
    $Livre->setISBN($ISBN);
    $Livre->setDateEdition(new \DateTime());
    
    $em = $this->getDoctrine()->getManager();
    $em->persist($Livre);
    $em->flush();
    return new View("Task Added Successfully", Response::HTTP_CREATED);
    }


    //Mise a jour d'un livre existant(dont on connait l'id)
    /**
    * @Rest\Put("/books/{id}")
    */
    public function updateLivre($id,Request $request)
    {
            $titre = $request->get('titre');
            $descriptif = $request->get('descriptif');
            $ISBN = $request->get('ISBN');
            $id_auteur = $request->get('id_auteur');
            //chercher l'auteur avec id donnee
            $em = $this->getDoctrine()->getManager();
            $auteur = $em->getRepository(Auteur::class)->find($id_auteur);
            //chercher le livre a mettre a jour 
            $em = $this->getDoctrine()->getManager();
            $Livre = $this->getDoctrine()->getRepository(Livre::class)->find($id);
            //tester s'il existe
        if (!$Livre){
        return new View("there are no Books for this ID", Response::HTTP_NOT_FOUND);
        }
        // modifier le titre seulement
        elseif(!empty($titre) &&  empty($descriptif) && empty($ISBN)){
            $Livre->setTitre($titre);
            $em->flush();
            return new View("Title update Successfully", Response::HTTP_CREATED);
        }
        //modifier le discriptif seulement
        elseif(empty($titre)&& !empty($descriptif) && empty($ISBN)){
            $Livre->setDescriptif($descriptif);
            $em->flush();
            return new View("Descriptif update Successfully", Response::HTTP_CREATED);
        }
        //modifier le ISBN seulement
        elseif(empty($titre)&& empty($descriptif) && !empty($ISBN)){
            $Livre->setISBN($ISBN);
            $em->flush();
            return new View("ISBN update Successfully", Response::HTTP_CREATED);
        }
        //modifier l'auteur seulement
        elseif(!empty($id_auteur) && empty($titre) &&  empty($descriptif) && empty($ISBN) && $auteur){
            $Livre->setAuteur($auteur);
            $em->flush();
            return new View("Author update Successfully", Response::HTTP_CREATED);
        }
        //tester si l'id d'auteur existe
        elseif(!$auteur){
            return new View("pas d'auteur avec cette id", Response::HTTP_NOT_FOUND);
        }
        elseif(empty($titre) || empty($descriptif) || empty($ISBN) || (empty($id_auteur))){
            return new View("verifier le remplissage de tous les champs [titre , descriptif , ISBN , id_auteur]", Response::HTTP_NOT_ACCEPTABLE);
        }
        //modifier tous les donnees 
        else{
            $Livre->setTitre($titre);
            $Livre->setDescriptif($descriptif);
            $Livre->setISBN($ISBN);
            $Livre->setDateEdition(new \DateTime());
            $Livre->setAuteur($auteur);
        $em->flush();
        return new View("Livre update Successfully", Response::HTTP_CREATED);
        }
    }
    //Suppression d'un livre
    /**
    * @Rest\Delete("/books/{id}")
    */
    public function SupLivre($id)
    {
    $data = $this->getDoctrine()->getRepository(Livre::class)->find($id);
    $em = $this->getDoctrine()->getManager();
    $em->remove($data);
    $em->flush();
    return new View("Livre supprimer Successfully", 202);
    }

}