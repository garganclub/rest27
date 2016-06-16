<?php

namespace Animaux\AnimauxBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use Symfony\Component\HttpFoundation\Request;
use Animaux\AnimauxBundle\Entity\Animal;

use Symfony\Component\Validator\Constraints\Length;
use Animaux\AnimauxBundle\Validator\Constraints\ValidationText;
use Animaux\AnimauxBundle\Validator\Constraints\ValidationTextValidator;

/**
 * class RestAPIController
 * API qui assure la gestion de la table "animal"
 * @RouteResource("animaux", pluralize=false)
 */
class RestAPIController extends Controller
{
    /**
     * @var array
     * champs de la table "animal"
     */
    private $donnees = array('classe', 'ordre', 'famille', 'nom');
    /**
     * @var array
     * expressions de test sur les entrées utilisateur
     */
    private $expressions = array(
    	'classe' => '/^[A-Za-zÀÂÄÉÈÊËÎÏÔÔÙÛÜÇàâäéèêëîïôöùûüç]+$/'
    );
    /**
     * @param integer $id
     * vérification que $id est bien un entier
     * renvoi des données de la ligne correspondant à $id
     * et de l' "entity manager"
     */
    private function ligne($id) {
    	if(preg_match('/^\d+$/', $id)) {
			// $id est bien un entier
			// récupération des données liées à $id
			$em = $this->getDoctrine()->getManager();
			$donnees = $em->getRepository('AnimauxBundle:Animal')->find($id);
			if(sizeOf($donnees) > 0) {
				// le résultat est non nul
				// envoi des données en json
				return array('donnees' => $donnees, 'manager' => $em);
			}
			else {
				throw $this->createNotFoundException(
					'L\'id n°'. $id .' n\'existe pas.'
				);
			}
		}
		else {
			throw $this->createNotFoundException(
				'L\'identifiant demandé : '. $id .' doit être un numéro.'
			);
		}
    }
    /**
     * @param array $data
     * vérification du contenu des champs
     * renvoyés par l'utilisateur
     */
    private function validation($data) {
    	$message = array();
    	$message['valide'] = true;
        foreach(($this->donnees) as $champ) {
        	// vérification des entrées dans les champs de l'utilisateur
			$validation = $this->get('validator')->validateValue(
				$data[$champ],
				new ValidationText()
			);
			if($validation->count() > 0) {
				// une erreur détectée
				$erreur = $validation->get(0);
				$message['valide'] = false;
				$message[$champ] = $erreur->getMessage();
			}
		}
		return $message;
    }
    /**
     * @param Animal $animal
     * @param array $data
     * hydratation des propriétés de l'objet "Animal"
     */
    private function hydratation(Animal $animal, array $data) {
        $animal->setClasse($data['classe']);
		$animal->setOrdre($data['ordre']);
		$animal->setFamille($data['famille']);
		$animal->setNom($data['nom']);
    }
    /**
     * @param Request $request
     * listing de la table "animal" avec ou sans filtre "classe"
     * méthode GET avec ou sans paramètres
     */
    public function cgetAction(Request $request)
    {
        // récupération de la requête
        $em = $this->getDoctrine()->getManager();
        $parametres = $request->query->all();
        if(sizeOf($parametres) == 0) {
        	// pas de paramètre GET -> pas de filtre
        	// listing complet de la table "animal" en json
        	$animaux = $em->getRepository('AnimauxBundle:Animal')->findBy(array(), array('id' => 'DESC'));
			if(sizeOf($animaux) > 0) {
				// il y a des données qui correspondent à la requête
				// listing de la table "animal" en json
				return $animaux;
			}
			else {
				throw $this->createNotFoundException(
					'La table : Animal est vide.'
				);
			}
        }
        else {
        	// présence d'un paramètre GET -> analyse de celui-ci
        	$classe = $request->query->get('classe');
        	if(sizeOf($parametres) == 1 && isset($classe)) {
        		// le paramètre classe existe
				if(preg_match(($this->expressions['classe']), $classe)) {
					// le paramètre classe retourne une valeur conforme
					// on filtre les données de la table "animal"
					$animaux = $em->getRepository('AnimauxBundle:Animal')->findByClasse($classe, array('id' => 'DESC'));
				}
				else {
					throw $this->createNotFoundException(
						'La classe demandée : '. $classe .' n\'est pas un nom.'
					);
				}
				if(sizeOf($animaux) > 0) {
					// il y a des données qui correspondent au filtrage
					// listing filtré de la table "animal" en json
					return $animaux;
				}
				else {
					throw $this->createNotFoundException(
						'La classe : '. $classe .' est vide.'
					);
				}
			}
			else {
				throw $this->createNotFoundException(
					'L\'adresse demandée n\'est pas valide.'
				);
			}
        }
    }
    /**
     * @param integer $id
     * présentation des données "animal" associée à $id
     * méthode GET
     */
    public function getAction($id)
    {
        // récupération des données de la ligne correspondant à $id
        $resultats = $this->ligne($id);
        return $resultats['donnees'];
    }
    /**
     * @param Request $request
     * création des données "animal"
     * méthode POST
     */
    public function postAction(Request $request)
    {
        // récupération de la requête et des données json
        $requete = $request->getContent();
        $data = json_decode($requete, true);
        $message = $this->validation($data);
        if($message['valide']) {
        	// création d'un objet entity à partir de la classe "Animal"
			$entree = new Animal();
			$this->hydratation($entree, $data);
			// enregistrement des données dans la table
			$em = $this->getDoctrine()->getManager();
			$em->persist($entree);
			$em->flush();
			// envoi des données en json
			return $entree;
        }
        else {
        	throw $this->createNotFoundException(json_encode($message));
        }
    }
    /**
     * @param Request $request
     * @param integer $id
     * modification des données "animal"
     * méthode PUT
     */
    public function putAction(Request $request, $id)
    {
        // récupération de la requête et des données json
        $requete = $request->getContent();
		$data = json_decode($requete, true);
		$message = $this->validation($data);
        if($message['valide']) {
			// récupération des données de la ligne correspondant à $id
			$resultats = $this->ligne($id);
        	$entree = $resultats['donnees'];
        	// modification des champs
			$this->hydratation($entree, $data);
			// enregistrement des modifications dans la table
			$em = $resultats['manager'];
			$em->persist($entree);
			$em->flush();
			// envoi des données en json
			return $entree;
		}
        else {
        	throw $this->createNotFoundException(json_encode($message));
        }
    }
    /**
     * @param integer $id
     * suppression des données "animal" associée à $id
     * méthode DELETE
     */
    public function deleteAction($id)
    {
        // récupération des données de la ligne correspondant à $id
		$resultats = $this->ligne($id);
		$entree = $resultats['donnees'];
		// suppression de la ligne correspondant à $id
		$em = $resultats['manager'];
		$em->remove($entree);
		$em->flush();
		// envoi des données en json
		return $entree;
    }
}