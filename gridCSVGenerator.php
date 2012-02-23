<?php

class gridHTMLGenerator {

	public $strip_tags	= false;
	protected $headerCols = Array();
	protected $footerCols = Array();

	public function printGrid($xml) {
		$this->renderStart();

		$this->headerParse($xml->head);
		$this->renderHeader($this->headerCols);
		$this->renderRows($xml->row);

		$this->footerParse($xml->foot);
		$this->renderFooter($this->footerCols);
		$this->renderEnd();
	}



	private function headerParse($header) {
		if (isset($header->column)) {
			$columns = Array($header->column);
		} else {
			$columns = $header->columns;
		}
		foreach ($columns as $row) {
			$cols = Array();
			foreach ($row as $column) {
				$hidden = ($column->attributes()->hidden == 'true') ? true : false;
				if ($hidden == true) {
					$this->hiddenCols[$k] = true;
					continue;
				}
				$col = $this->strip(trim((string) $column));
				$cols[] = $col;
			}
			$this->headerCols[] = $cols;
		}
	}
	
	
	private function renderStart() {
		header("Content-type: application/vnd.ms-excel; charset=UTF-8");
		header("Content-Disposition: attachment;filename=grid.xls");
		header("Cache-Control: max-age=0");
	}

	private function renderEnd() { }
	
	private function renderHeader($cols) {
		for ($i = 0; $i < count($cols); $i++) {
			for ($j = 0; $j < count($cols[$i]); $j++) {
				echo $cols[$i][$j]."\t";
			}
			echo "\n";
		}
	}


	private function footerParse($footer) {
		if (isset($footer->columns)) {
			$columns = $footer->columns;
			foreach ($columns as $row) {
				$cols = Array();
				foreach ($row as $column) {
					$col = $this->strip(trim((string) $column));
					$cols[] = $col;
				}
				$this->footerCols[] = $cols;
			}
		}
	}
	
	
	private function renderFooter($cols) {
		for ($i = 0; $i < count($cols); $i++) {
			for ($j = 0; $j < count($cols[$i]); $j++)
				echo $cols[$i][$j]."\t";
			echo "\n";
		}
	}

	
	private function renderRows($rows) {
		$i = 0;
		foreach ($rows as $row) {
			$className = ($i%2 == 0) ? "cell_even" : "cell_odd";
			$j = 0;
			foreach ($row as $cell) {
				if (isset($this->hiddenCols[$j])) {
					$j++;
					continue;
				}

				$text = $this->strip((string) $cell);
				echo $text."\t";
				$j++;
			}
			echo "\n";
			$i++;
		}
	}

	private function strip($param) {
		if ($this->strip_tags == true) {
			$param = strip_tags($param);
		}

		return trim($param);
	}

}

?>