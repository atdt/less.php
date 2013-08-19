<?php

namespace Less\Node;

class Operation{
	public function __construct($op, $operands, $isSpaced = false){
		$this->op = trim($op);
		$this->operands = $operands;
		$this->isSpaced = $isSpaced;
	}

	public function compile($env){
		$a = $this->operands[0]->compile($env);
		$b = $this->operands[1]->compile($env);


		if( $env->isMathsOn() ){
			if( $a instanceof \Less\Node\Dimension && $b instanceof \Less\Node\Color ){
				if ($this->op === '*' || $this->op === '+') {
					$temp = $b;
					$b = $a;
					$a = $temp;
				} else {
					throw new \Less\CompilerError("Can't subtract or divide a color from a number");
				}
			}
			if ( !$a || !method_exists($a,'operate') ) {
				throw new \Less\CompilerError("Operation on an invalid type");
			}

			return $a->operate($env,$this->op, $b);
		} else {
			return new \Less\Node\Operation($this->op, array($a, $b), $this->isSpaced );
		}
	}

	function toCSS($env){
		$separator = $this->isSpaced ? " " : "";
		return $this->operands[0]->toCSS($env) . $separator . $this->op . $separator . $this->operands[1]->toCSS($env);
	}
}
