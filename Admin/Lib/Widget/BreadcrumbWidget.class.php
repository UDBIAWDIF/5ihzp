<?php

class BreadcrumbWidget extends Widget {

	public function render($data) {
		$content = $this->renderFile('',$data);
		return $content;
	}

}