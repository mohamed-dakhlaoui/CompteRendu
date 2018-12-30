<?php
namespace AppBundle\DataFixtures\ORM;
use AppBundle\Entity\Livre;
use AppBundle\Entity\Auteur;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
class AppFixtures implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        for ($i = 0; $i < 10; $i++) {
            $Auteur = new Auteur();
            $Auteur->setNom('Nom'.$i);
            $Auteur->setPrenom('Prenom'.$i);
            $Auteur->setEmail('email'.$i);
            $manager->persist($Auteur);

            $Livre = new Livre();
            $Livre->setTitre('Titre'.$i);
            $Livre->setDescriptif('Description'.$i);
            $Livre->setISBN('ISBN'.$i);
            $Livre->setDateEdition(new \DateTime());
            $Livre->setAuteur($Auteur);
            
            $manager->persist($Livre);
            
        }
        $manager->flush();
    }
}
?>