Gridito
=======

- Autor: Jan Marek, Vít Šesták
- Licence: MIT

FORK Z http://github.com/janmarek/Gridito

Zjednodušený ukázkový Presenter (má pár nedostatků):

<?php
use \Nette\Environment;
use \Nette\Forms\Form;
use \Nette\Application\AppForm;
use \Gridito\EditableGrid;
use \Nette\Debug;
use \Nette\ArrayTools;

class GridPresenter extends BasePresenter{

	private $model;

	protected function beforeRender() {
		$session = Environment::getSession();
		if (!$session->isStarted()) $session->start();
	}

    protected function createComponentGrid($name) {
		$grid = new EditableGrid();
		$presenter = $this;
		
		$this->model = ColorsModel::getInstance();
		
		$grid->setEditableModel($this->model, function (AppForm $form) { // This should be simplified.
			$form->addText("hash", "Hexadecimální kód", 6, 6)
				->addRule(Form::REGEXP, "Použijte šest znaků 0-F", "/[0-9A-Fa-f]{6}/");
			$form->addText("description", "Popis", 30, 100);
			$form->addCheckbox("nice", "Je krásná");
			$form->addSubmit("s", "Uložit");
		});
		$grid->setFormFilter(function($vals, $form){
			return array('nice'=>var_export($vals['nice'], true))+$vals;
		});
		$grid->setDefaultValueFilter(function($vals){
			return array('nice'=>ArrayTools::get(array('true'=>true, 'false'=>false), $vals['nice']))+(array)$vals;
		});
		$grid->setSavedMessage("Barva byla uložena.");
		$grid->setRemovedMessage("Barva byla smazána.");
		$grid->setItemsPerPage(5);

		$grid->addColumn("hash", "Hexadecimální kód", function ($record) {
			echo "<span style='color:#$record->hash'>#$record->hash</span>";
		})->setSortable(true);
		$grid->addColumn("description", "Popis");
		$grid->addColumn("nice", "Je krásná")->setSortable(true);
		$grid->addColumn("created", "Vytvořeno")->setSortable(true);
		
		$grid->addAddButton("Přidat", "plusthick");
		$grid->addEditButton("Upravit", "pencil");
		$grid->addRemoveButton("Smazat", "closethick")->setConfirmationQuestion("Opravdu smazat barvu?");

		return $grid;
	}

}

Ukázkový model:
<?php
use Gridito\DibiEditableModel;


final class ColorsModel{

	public static function getInstance(){
		return DibiEditableModel::factory('colors')
			->setCreateHandler(function($vals){
				return $vals+array('created'=>time());
			})
			->build();
	}

}
