<?php

namespace Animaux\AnimauxBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class ValidationText extends Constraint
{
	public $messageCaracteres = 'Vous avez utilisé des caractères interdits.';
	public $messageDebut = 'Le nom doit commencer par une lettre.';
	public $messageFin = 'Le nom doit finir par une lettre.';
	public $messageDoublon = 'Doublon d\'espaces, tirets ou apostrophes.';
	public $messageApostrophe = 'Placer les apostrophes entre des lettres.';
	public $messageMinimumCaracteres = 'Vous devez renseigner au moins 2 caractères.';
	public $messageMaximumCaracteres = 'Vous ne pouvez pas dépasser 50 caractères.';
}