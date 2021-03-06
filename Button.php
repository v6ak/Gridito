<?php

namespace Gridito;

/**
 * Action button
 *
 * @author Jan Marek
 * @license MIT
 */
class Button extends BaseButton {

	// <editor-fold defaultstate="collapsed" desc="variables">

	/** @var bool */
	private $ajax = false;

	/** @var string|false */
	private $confirmationQuestion = false;

	// </editor-fold>
	
	// <editor-fold defaultstate="collapsed" desc="getters & setters">

	/**
	 * Is ajax?
	 * @return bool
	 */
	public function isAjax() {
		return $this->ajax;
	}


	/**
	 * Set ajax mode
	 * @param bool $ajax
	 * @return Button
	 */
	public function setAjax($ajax) {
		$this->ajax = (bool) $ajax;
		return $this;
	}


	/**
	 * Get confirmation question
	 * @return string|false
	 */
	public function getConfirmationQuestion() {
		return $this->confirmationQuestion;
	}


	/**
	 * Set confirmation question
	 * @param string|false $confirmationQuestion
	 * @return Button
	 */
	public function setConfirmationQuestion($confirmationQuestion) {
		$this->confirmationQuestion = $confirmationQuestion;
		return $this;
	}
	
	// </editor-fold>

	// <editor-fold defaultstate="collapsed" desc="signal">

	/**
	 * Handle click signal
	 * @param string $token security token
	 * @param mixed $pk
	 */
	public function handleClick($token, $pk = null) {
		parent::handleClick($token, $pk);

		if ($this->getPresenter()->isAjax()) {
			$this->getGrid()->invalidateControl();
		} else {
			$this->getGrid()->redirect("this");
		}
	}

	// </editor-fold>

	// <editor-fold defaultstate="collapsed" desc="helpers">

	/**
	 * Create button element
	 * @param mixed $row
	 * @return \Nette\Web\Html
	 */
	protected function createButton($row = null) {
		$el = parent::createButton($row);

		if ($this->ajax) {
			$el->class($this->getGrid()->getAjaxClass());
		}

		if ($this->confirmationQuestion) {
			$el->onClick = "gridito.confirmationQuestion(event, " . json_encode($this->confirmationQuestion) . ")";
		}
		
		return $el;
	}

	// </editor-fold>

}