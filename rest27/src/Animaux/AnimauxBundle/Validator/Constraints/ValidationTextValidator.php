<?php

namespace Animaux\AnimauxBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ValidationTextValidator extends ConstraintValidator
{
	public function validate($value, Constraint $constraint) {
		if(preg_match('/^.{2,}$/', $value) === 0) {
			$this->context->addViolation($constraint->messageMinimumCaracteres);
		}
		else if(preg_match('/^[A-Za-zÀÂÄÉÈÊËÎÏÔÔÙÛÜÇàâäéèêëîïôöùûüç]/', $value) === 0) {
			$this->context->addViolation($constraint->messageDebut);
		}
		else if(preg_match('/  |--|\'\'/', $value) !== 0) {
			$this->context->addViolation($constraint->messageDoublon);
		}
		else if(preg_match('/\' | \'|\'-|-\'/', $value) !== 0) {
			$this->context->addViolation($constraint->messageApostrophe);
		}
		else if(preg_match('/^[A-Za-zÀÂÄÉÈÊËÎÏÔÔÙÛÜÇàâäéèêëîïôöùûüç \'-]+$/', $value) === 0) {
			$this->context->addViolation($constraint->messageCaracteres);
		}
		else if(preg_match('/^.{0,50}$/', $value) === 0) {
			$this->context->addViolation($constraint->messageMaximumCaracteres);
		}
		else if(preg_match('/[A-Za-zÀÂÄÉÈÊËÎÏÔÔÙÛÜÇàâäéèêëîïôöùûüç]$/', $value) === 0) {
			$this->context->addViolation($constraint->messageFin);
		}
	}
}